<?php

namespace App\Http\Controllers;

use App\Models\Games\Game;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ConnectedAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Create a brand-new connected profile under the authenticated user.
     */
    public function store(Request $request)
    {
        $manager = Auth::user();

        // Rule: A managed profile cannot manage other profiles!
        if (!$manager->canManageOthers()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'A connected profile cannot manage other profiles.'], 403);
            }
            return back()->withErrors(['error' => 'A connected profile cannot manage other profiles.']);
        }

        $data = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'role_preference' => 'required|string|in:player,goalie',
            'jersey_number' => 'nullable|integer|min:0|max:99',
            'level' => 'nullable|integer|min:1|max:5',
            'mobility' => 'nullable|integer|min:1|max:5',
        ]);

        $name = Str::title(Str::lower(trim($data['name'])));

        // Create the managed user
        $managedUser = User::create([
            'name' => $name,
            'manager_id' => $manager->id,
            'role_preference' => $data['role_preference'],
            'jersey_number' => $data['jersey_number'] ?? null,
            'level' => $data['level'] ?? 3,
            'mobility' => $data['mobility'] ?? 3,
            'completed_onboarding' => true,
            'notify_team_assignment' => true,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Connected profile for {$managedUser->name} created successfully!",
                'profile' => $managedUser,
            ]);
        }

        return back()->with('success', "Connected profile for {$managedUser->name} created successfully!");
    }

    /**
     * Take control of an existing guest and convert them into a permanent connected profile.
     */
    public function claimGuest(Request $request)
    {
        $manager = Auth::user();

        if (!$manager->canManageOthers()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'A connected profile cannot manage other profiles.'], 403);
            }
            return back()->withErrors(['error' => 'A connected profile cannot manage other profiles.']);
        }

        $data = $request->validate([
            'guest_name' => 'required|string|min:2|max:100',
            'jersey_number' => 'nullable|integer|min:0|max:99',
            'role_preference' => 'nullable|string|in:player,goalie',
            'level' => 'nullable|integer|min:1|max:5',
        ]);

        $guestName = Str::title(Str::lower(trim($data['guest_name'])));

        // Check if manager already has a profile with this name
        if (User::where('manager_id', $manager->id)->where('name', $guestName)->exists()) {
            $msg = "You already manage a connected profile named {$guestName}.";
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => $msg], 422);
            }
            return back()->withErrors(['guest_name' => $msg]);
        }

        // Fetch last known level and role from past guest games
        $lastGuestRecord = DB::table('game_players_guests')
            ->where('name', $guestName)
            ->orderByDesc('id')
            ->first();

        $level = $data['level'] ?? ($lastGuestRecord->level ?? 3);
        $role = $data['role_preference'] ?? ($lastGuestRecord->role ?? 'player');

        $managedUser = DB::transaction(function () use ($guestName, $manager, $role, $level, $data) {
            $user = User::create([
                'name' => $guestName,
                'manager_id' => $manager->id,
                'role_preference' => $role,
                'jersey_number' => $data['jersey_number'] ?? null,
                'level' => $level,
                'mobility' => 3,
                'completed_onboarding' => true,
                'notify_team_assignment' => true,
            ]);

            // Link all past games where this guest played to the new user account
            $pastGuestEntries = DB::table('game_players_guests')->where('name', $guestName)->get();
            foreach ($pastGuestEntries as $entry) {
                // Ensure record exists in game_players
                DB::table('game_players')->updateOrInsert(
                    ['user_id' => $user->id, 'game_id' => $entry->game_id],
                    ['role' => $entry->role]
                );

                // Ensure team assignment is preserved
                $teamGuest = DB::table('game_teams_guests')->where('guest_id', $entry->id)->first();
                if ($teamGuest) {
                    DB::table('game_teams_players')->updateOrInsert(
                        ['game_id' => $entry->game_id, 'user_id' => $user->id],
                        ['team' => $teamGuest->team]
                    );
                }
            }

            return $user;
        });

        $msg = "Successfully took control of {$managedUser->name}! They are now a connected profile under your account with past games linked.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'profile' => $managedUser,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Update details of a connected profile (jersey, position, level, name).
     */
    public function update(Request $request, User $connectedUser)
    {
        $manager = Auth::user();

        // Enforce ownership
        if ((int) $connectedUser->manager_id !== (int) $manager->id) {
            abort(403, 'Unauthorized management access.');
        }

        $data = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'role_preference' => 'required|string|in:player,goalie',
            'jersey_number' => 'nullable|integer|min:0|max:99',
            'level' => 'nullable|integer|min:1|max:5',
        ]);

        $connectedUser->name = Str::title(Str::lower(trim($data['name'])));
        $connectedUser->role_preference = $data['role_preference'];
        $connectedUser->jersey_number = $data['jersey_number'] ?? null;
        if (isset($data['level'])) {
            $connectedUser->level = $data['level'];
        }
        $connectedUser->save();

        $msg = "Connected profile for {$connectedUser->name} updated successfully!";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'profile' => $connectedUser,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Unlink a connected profile and promote it to an independent account with username and password.
     */
    public function promoteToIndependent(Request $request, User $connectedUser)
    {
        $manager = Auth::user();

        // Enforce ownership
        if ((int) $connectedUser->manager_id !== (int) $manager->id) {
            abort(403, 'Unauthorized management access.');
        }

        $data = $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $connectedUser->email = strtolower(trim($data['email']));
        $connectedUser->password = Hash::make($data['password']);
        $connectedUser->email_verified_at = now();
        $connectedUser->manager_id = null; // Unlink completely from manager
        $connectedUser->save();

        $msg = "{$connectedUser->name} has been unlinked from your account and is now an independent account! They can now log in directly using {$connectedUser->email}.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Remove a connected profile.
     */
    public function destroy(Request $request, User $connectedUser)
    {
        $manager = Auth::user();

        if ((int) $connectedUser->manager_id !== (int) $manager->id) {
            abort(403, 'Unauthorized management access.');
        }

        $name = $connectedUser->name;

        // If the user has past games, simply unlink them without breaking history, otherwise delete
        $hasGames = DB::table('game_players')->where('user_id', $connectedUser->id)->exists();
        if ($hasGames) {
            $connectedUser->manager_id = null;
            $connectedUser->save();
            $msg = "{$name} has been removed from your connected profiles list.";
        } else {
            $connectedUser->delete();
            $msg = "Connected profile {$name} has been deleted.";
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * RSVP or add a connected profile to a specific game.
     */
    public function rsvpConnected(Request $request, Game $game, User $connectedUser)
    {
        $manager = Auth::user();

        if ((int) $connectedUser->manager_id !== (int) $manager->id) {
            abort(403, 'Unauthorized management access.');
        }

        $data = $request->validate([
            'role' => 'required|string|in:player,goalie',
        ]);

        $role = $data['role'];

        // Enforce max 2 goalies per game
        if ($role === 'goalie') {
            $userGoalies = DB::table('game_players')
                ->where('game_id', $game->id)
                ->where('role', 'goalie')
                ->where('user_id', '!=', $connectedUser->id)
                ->count();
            $guestGoalies = DB::table('game_players_guests')
                ->where('game_id', $game->id)
                ->where('role', 'goalie')
                ->count();

            if (($userGoalies + $guestGoalies) >= 2) {
                $err = 'Goalie spots are full for this game (maximum 2 goalies).';
                if ($request->expectsJson() || $request->ajax()) return response()->json(['error' => $err], 422);
                return back()->withErrors(['role' => $err]);
            }
        }

        // Add or update attendance
        DB::table('game_players')->updateOrInsert(
            ['user_id' => $connectedUser->id, 'game_id' => $game->id],
            ['role' => $role]
        );

        $msg = "{$connectedUser->name} has been signed up for this game as " . ucfirst($role) . "!";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Remove a connected profile from a specific game.
     */
    public function removeConnectedFromGame(Request $request, Game $game, User $connectedUser)
    {
        $manager = Auth::user();

        if ((int) $connectedUser->manager_id !== (int) $manager->id) {
            abort(403, 'Unauthorized management access.');
        }

        DB::transaction(function () use ($connectedUser, $game) {
            DB::table('game_players')
                ->where('user_id', $connectedUser->id)
                ->where('game_id', $game->id)
                ->delete();

            DB::table('game_teams_players')
                ->where('user_id', $connectedUser->id)
                ->where('game_id', $game->id)
                ->delete();
        });

        $msg = "{$connectedUser->name} has been removed from this game.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }
}
