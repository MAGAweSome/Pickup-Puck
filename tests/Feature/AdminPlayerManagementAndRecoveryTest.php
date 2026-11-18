<?php

namespace Tests\Feature;

use App\Mail\AccountRecoveryMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPlayerManagementAndRecoveryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $player;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->admin = User::factory()->create([
            'name' => 'Admin Boss',
            'email' => 'admin@pickuppuck.ca',
            'password' => Hash::make('AdminSecret123!'),
            'email_verified_at' => now(),
            'completed_onboarding' => true,
        ]);
        $this->admin->assignRole('admin');

        $this->player = User::factory()->create([
            'name' => 'Connor Skater',
            'email' => 'connor@pickuppuck.ca',
            'password' => Hash::make('OldPassword123!'),
            'email_verified_at' => now(),
            'completed_onboarding' => true,
            'level' => 3,
            'role_preference' => 'skater',
        ]);
    }

    public function test_non_admin_cannot_access_admin_user_endpoints(): void
    {
        $this->actingAs($this->player);

        $this->get(route('user_list'))->assertStatus(403);
        $this->post(route('user_role_update.user_id', $this->player), [
            'name' => 'Hacked Name',
            'email' => $this->player->email,
        ])->assertStatus(403);
        $this->delete(route('admin.user.destroy', $this->player))->assertStatus(403);
        $this->post(route('admin.user.recovery_link', $this->player))->assertStatus(403);
        $this->post(route('admin.user.recovery_email', $this->player))->assertStatus(403);
    }

    public function test_admin_can_edit_player_without_touching_password(): void
    {
        $originalPasswordHash = $this->player->password;

        $response = $this->actingAs($this->admin)
            ->postJson(route('user_role_update.user_id', $this->player), [
                'name' => 'Connor Updated',
                'email' => 'connor.updated@pickuppuck.ca',
                'gameRole' => 'goalie',
                'level' => 4,
                'password' => 'AttemptedOverwrite123!',
            ]);

        $response->assertOk()->assertJsonPath('status', 'success');

        $this->player->refresh();
        $this->assertSame('Connor Updated', $this->player->name);
        $this->assertSame('connor.updated@pickuppuck.ca', $this->player->email);
        $this->assertSame('goalie', $this->player->role_preference);
        $this->assertSame(4, $this->player->level);
        // Zero Direct Admin Password Editing: password hash must remain untouched
        $this->assertSame($originalPasswordHash, $this->player->password);
    }

    public function test_admin_can_permanently_delete_player_with_game_references_and_site_stays_healthy(): void
    {
        $gameId = DB::table('games')->insertGetId([
            'title' => 'Tuesday Night Shinny',
            'time' => now()->addDays(2),
            'location' => 'Southwood Arena',
            'price' => 20.0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('game_players')->insert([
            'game_id' => $gameId,
            'user_id' => $this->player->id,
            'role' => 'player',
        ]);

        DB::table('game_payments')->insert([
            'game_id' => $gameId,
            'user_id' => $this->player->id,
            'payment' => 20,
            'method' => 'etransfer',
        ]);

        DB::table('game_player_responses')->insert([
            'game_id' => $gameId,
            'user_id' => $this->player->id,
            'status' => 'cannot',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->deleteJson(route('admin.user.destroy', $this->player))
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseMissing('users', ['id' => $this->player->id]);
        $this->assertDatabaseMissing('game_players', ['user_id' => $this->player->id]);
        $this->assertDatabaseMissing('game_payments', ['user_id' => $this->player->id]);
        $this->assertDatabaseMissing('game_player_responses', ['user_id' => $this->player->id]);

        // Core pages still render cleanly after player deletion
        $this->actingAs($this->admin)->get(route('user_list'))->assertOk();
        $this->actingAs($this->admin)->get(route('home'))->assertOk();
        $this->actingAs($this->admin)->get(route('games.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('game_detail.game_id', $gameId))->assertOk();
    }

    public function test_recovery_link_copy_and_email_work_together_and_single_use_self_service_updates_account(): void
    {
        Mail::fake();

        // 1. Admin generates 5-hour signed link (Copy to Clipboard flow)
        $genRes = $this->actingAs($this->admin)
            ->postJson(route('admin.user.recovery_link', $this->player));
        $genRes->assertOk()->assertJsonStructure(['url', 'expires_at']);
        $copiedUrl = $genRes->json('url');

        // 2. Admin sends email using the same generated link (must not invalidate copiedUrl)
        $emailRes = $this->actingAs($this->admin)
            ->postJson(route('admin.user.recovery_email', $this->player), [
                'recovery_url' => $copiedUrl,
            ]);
        $emailRes->assertOk()->assertJsonPath('url', $copiedUrl);

        Mail::assertSent(AccountRecoveryMail::class, function (AccountRecoveryMail $mail) use ($copiedUrl) {
            return $mail->hasTo($this->player->email) && $mail->url === $copiedUrl;
        });

        // 3. Log out admin and open the public self-service link as a guest
        auth()->logout();

        $this->get($copiedUrl)
            ->assertOk()
            ->assertSee('Update Your Account')
            ->assertSee($this->player->name)
            ->assertSee($this->player->email);

        // 4. Tampered signature is rejected with custom 403 error page
        $this->get($copiedUrl . 'tampered')
            ->assertStatus(403)
            ->assertSee('This Account Update Link Has Expired');

        // 5. Submit valid profile & password update
        $parsedQuery = [];
        parse_str((string) parse_url($copiedUrl, PHP_URL_QUERY), $parsedQuery);

        $submitRes = $this->post($copiedUrl, [
            'token' => $parsedQuery['token'] ?? '',
            'name' => 'Connor SelfUpdated',
            'email' => 'connor.self@pickuppuck.ca',
            'password' => 'NewIcePassword2026!',
            'password_confirmation' => 'NewIcePassword2026!',
        ]);
        $submitRes->assertRedirect(route('login'));

        $this->player->refresh();
        $this->assertSame('Connor SelfUpdated', $this->player->name);
        $this->assertSame('connor.self@pickuppuck.ca', $this->player->email);
        $this->assertTrue(Hash::check('NewIcePassword2026!', $this->player->password));
        $this->assertNull($this->player->account_recovery_token);

        // 6. Single-use check: reopening link after save returns custom 403 expired view
        $this->get($copiedUrl)
            ->assertStatus(403)
            ->assertSee('This Account Update Link Has Expired');

        // 7. Expired signature (>5 hours ago) returns custom 403 expired view
        $expiredUrl = URL::temporarySignedRoute(
            'account.recovery.show',
            now()->subMinute(),
            ['user' => $this->player->id, 'token' => 'expiredtoken']
        );
        $this->get($expiredUrl)
            ->assertStatus(403)
            ->assertSee('This Account Update Link Has Expired');
    }
}
