<?php

namespace App\Http\Controllers;

use App\Enums\Games\GameRoles;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Mail\AccountRecoveryMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class UserRoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Show the dedicated admin edit player page.
     */
    public function index(User $user)
    {
        return view('user_role', ['user' => $user, 'GAME_ROLES' => GameRoles::cases()]);
    }

    /**
     * Update player profile metadata (never touches password directly).
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if ($request->filled('gameRole')) {
            $user->role_preference = $request->input('gameRole');
        }

        if ($request->filled('level')) {
            $user->level = (int) $request->input('level');
        }

        if ($request->boolean('adminCheck')) {
            $user->assignRole('admin');
        } else {
            $user->removeRole('admin');
        }

        $user->save();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Successfully updated {$user->name}'s profile.",
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_preference' => $user->role_preference,
                    'level' => (int) ($user->level ?? 3),
                    'is_admin' => $user->hasRole('admin'),
                ],
            ]);
        }

        return back()->with('success', "You have successfully changed {$user->name}'s profile!");
    }

    /**
     * Permanently delete a player and cleanly remove/cascade all foreign-key references.
     */
    public function destroy(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            $msg = 'You cannot delete your own administrator account.';
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $deletedName = $user->name;

        DB::transaction(function () use ($user) {
            $userId = $user->id;

            // Clean up game signups, team assignments, payments, and responses
            if (Schema::hasTable('game_players')) {
                DB::table('game_players')->where('user_id', $userId)->delete();
            }
            if (Schema::hasTable('game_teams_players')) {
                DB::table('game_teams_players')->where('user_id', $userId)->delete();
            }
            if (Schema::hasTable('game_payments')) {
                DB::table('game_payments')->where('user_id', $userId)->delete();
            }
            if (Schema::hasTable('game_player_responses')) {
                DB::table('game_player_responses')->where('user_id', $userId)->delete();
            }

            // Clean up push subscriptions and after-game events
            if (Schema::hasTable('push_subscriptions')) {
                DB::table('push_subscriptions')->where('user_id', $userId)->delete();
            }
            if (Schema::hasTable('after_game_event_attendees')) {
                DB::table('after_game_event_attendees')->where('user_id', $userId)->delete();
            }
            if (Schema::hasTable('after_game_events')) {
                DB::table('after_game_events')->where('creator_id', $userId)->delete();
            }

            // Detach any managed profiles rather than failing FK constraints
            if (Schema::hasColumn('users', 'manager_id')) {
                User::where('manager_id', $userId)->update(['manager_id' => null]);
            }

            // Clean up Spatie roles/permissions and Sanctum tokens
            $user->roles()->detach();
            $user->permissions()->detach();
            if (Schema::hasTable('personal_access_tokens')) {
                DB::table('personal_access_tokens')
                    ->where('tokenable_type', User::class)
                    ->where('tokenable_id', $userId)
                    ->delete();
            }

            $user->delete();
        });

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "{$deletedName} has been permanently deleted.",
            ]);
        }

        return redirect()
            ->route('user_list')
            ->with('success', "{$deletedName} has been permanently deleted.");
    }

    /**
     * Issue a fresh 5-hour single-use signed self-service recovery URL for the player.
     */
    protected function issueSignedRecoveryLink(User $user): array
    {
        $plainToken = Str::random(48);
        $expiresAt = now()->addHours(5);

        $user->account_recovery_token = hash('sha256', $plainToken);
        $user->account_recovery_expires_at = $expiresAt;
        $user->save();

        $url = URL::temporarySignedRoute(
            'account.recovery.show',
            $expiresAt,
            [
                'user' => $user->id,
                'token' => $plainToken,
            ]
        );

        return [
            'url' => $url,
            'expires_at' => $expiresAt->toIso8601String(),
            'expires_human' => $expiresAt->format('M j, Y g:i A'),
        ];
    }

    /**
     * Admin endpoint: Generate a 5-hour self-service link for copying to clipboard.
     */
    public function generateRecoveryLink(Request $request, User $user)
    {
        $issued = $this->issueSignedRecoveryLink($user);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'url' => $issued['url'],
                'expires_at' => $issued['expires_at'],
                'expires_human' => $issued['expires_human'],
            ]);
        }

        return back()->with('recovery_link', $issued['url']);
    }

    /**
     * Admin endpoint: Send the 5-hour recovery link via email.
     * Reuses an already-generated active link if provided and valid so Copy & Email
     * never invalidate each other prematurely.
     */
    public function sendRecoveryEmail(Request $request, User $user)
    {
        $existingUrl = (string) $request->input('recovery_url', '');
        $urlToSend = null;
        $expiresHuman = null;

        if (
            $existingUrl !== '' &&
            !empty($user->account_recovery_token) &&
            !empty($user->account_recovery_expires_at) &&
            now()->lessThan($user->account_recovery_expires_at)
        ) {
            $parsedQuery = [];
            $queryStr = parse_url($existingUrl, PHP_URL_QUERY) ?? '';
            parse_str($queryStr, $parsedQuery);
            $providedToken = (string) ($parsedQuery['token'] ?? '');

            if ($providedToken !== '' && hash_equals($user->account_recovery_token, hash('sha256', $providedToken))) {
                $urlToSend = $existingUrl;
                $expiresHuman = $user->account_recovery_expires_at->format('M j, Y g:i A');
            }
        }

        if ($urlToSend === null) {
            $issued = $this->issueSignedRecoveryLink($user);
            $urlToSend = $issued['url'];
            $expiresHuman = $issued['expires_human'];
        }

        Mail::to($user->email)->send(new AccountRecoveryMail($user, $urlToSend));

        $msg = "Recovery link emailed to {$user->email} (expires in 5 hours).";
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'url' => $urlToSend,
                'expires_human' => $expiresHuman,
                'message' => $msg,
            ]);
        }

        return back()
            ->with('success', $msg)
            ->with('recovery_link', $urlToSend);
    }
}
