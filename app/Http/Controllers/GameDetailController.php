<?php

namespace App\Http\Controllers;

use App\Enums\Games\GameRoles;
use App\Helpers\EmailHelper;
use App\Http\Requests\Admin\UserAcceptGamePayment;
use App\Http\Requests\Admin\UserAcceptGameRequest;
use App\Http\Requests\Admin\UserAcceptGameRequestGuest;
use App\Models\Games\Game;
use App\Models\Games\GamePayment;
use App\Models\Games\GamePlayer;
use App\Models\Games\GamePlayersGuest;
use App\Models\Games\GameTeamsPlayer;
use App\Models\Games\GameTeamsGuest;
use App\Models\User;
use App\Models\Guest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\GameTeamsService;

class GameDetailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['downloadIcs']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Game $game)
    {

        $user_registered = $game->gamePlayers()->wherePivot('user_id', Auth::user()->id)->exists();
        $user_paid = $game->gamePayments()->wherePivot('user_id', Auth::user()->id)->exists();

        $players = $game->players->pluck('name', 'id')->toArray();
        // goalies as id => name so we can reference the user id in views
        $goalies = $game->goalies->pluck('name', 'id');
        $user_is_a_goalie = False;
        $users = User::all();
        $guests = Guest::all();

        $attendingUserIds = DB::table('game_players')
            ->where('game_id', $game->id)
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        // Users who explicitly marked 'cannot' for this game
        $cannotAttendingIds = DB::table('game_player_responses')
            ->where('game_id', $game->id)
            ->where('status', 'cannot')
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        // Exclude both attending users and users who have indicated they cannot attend
        $excludedIds = array_unique(array_merge($attendingUserIds, $cannotAttendingIds));

        $notAttendingUsers = User::query()
            ->whereNotIn('id', $excludedIds)
            ->orderBy('name')
            ->get();

        // Users who explicitly marked 'cannot' for this game
        $cannotAttendingIds = DB::table('game_player_responses')
            ->where('game_id', $game->id)
            ->where('status', 'cannot')
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $cannotAttendingUsers = User::query()
            ->whereIn('id', $cannotAttendingIds)
            ->orderBy('name')
            ->get();

        $currentTime = Carbon::now()->setTimezone('America/Toronto');
        $teamData = $this->getTeamsData($game);
        $teamsRevealAt = $teamData['teamsRevealAt'];
        $teamsReady = $teamData['teamsReady'];
        $currentUserTeam = $teamData['currentUserTeam'];
        $darkTeamMembers = $teamData['darkTeamMembers'];
        $lightTeamMembers = $teamData['lightTeamMembers'];
        $darkTeamSkill = $teamData['darkTeamSkill'];
        $lightTeamSkill = $teamData['lightTeamSkill'];

        // return guest records so we have ids and names available for admin actions
        $guestPlayers = DB::table('game_players_guests')->where('game_id', $game->id)->where('role', 'player')->get();
        $guestGoalies = DB::table('game_players_guests')->where('game_id', $game->id)->where('role', 'goalie')->get();


        $players_attending = array();

        foreach ($players as $player){
            array_push($players_attending, $player);
        }
        
        foreach ($goalies as $goalie_id => $goalie_name){
            array_push($players_attending, $goalie_name);

            if (Auth::user()->name == $goalie_name){
                $user_is_a_goalie = TRUE;
            }
        }

        // Generate email data for admin: all registered players on website
        $allPlayersForEmail = User::where('email', '!=', null)
            ->where('email', '!=', '')
            ->orderBy('name')
            ->get();
        $smartDateMessage = EmailHelper::getSmartDateMessage($game->time);
        $emailSubject = 'Upcoming Game Reminder';
        $emailBody = EmailHelper::generateEmailBody($game->title, $smartDateMessage);
        $mailtoLink = EmailHelper::generateMailtoLink($allPlayersForEmail, $emailSubject, $emailBody);

        // $current_game_price_percentage = 100*($game->collected_game_cost/$game->ice_cost);

        return view('game_detail', [
            'game' => $game,
            'GAME_ROLES' => GameRoles::cases(),
            'players' => $players,
            'goalies' => $goalies,
            'user_registered' => $user_registered,
            'user_paid' => $user_paid,
            // 'current_game_price_percentage' => $current_game_price_percentage,
            'users' => $users,
            'guests' => $guests,
            'players_attending' => $players_attending,
            'user_is_a_goalie' => $user_is_a_goalie,
            'darkTeamMembers' => $darkTeamMembers,
            'lightTeamMembers' => $lightTeamMembers,
            'darkTeamSkill' => $darkTeamSkill,
            'lightTeamSkill' => $lightTeamSkill,
            'currentTime' => $currentTime,
            'teamsRevealAt' => $teamsRevealAt,
            'teamsReady' => $teamsReady,
            'currentUserTeam' => $currentUserTeam,
            'currentSeason' => $game->season,
            'guestPlayers' => $guestPlayers,
            'guestGoalies' => $guestGoalies,
            'notAttendingUsers' => $notAttendingUsers,
            'cannotAttendingUsers' => $cannotAttendingUsers,
            'emailMailtoLink' => $mailtoLink,
            'emailSubject' => $emailSubject,
            'emailBody' => $emailBody,
        ]);

    }

    public function update(UserAcceptGameRequest $request, Game $game) {
        $role = $request->input('gameRole');
        $userId = $request->user()->id;

        // Check if user is already signed up for this game
        $alreadySignedUp = DB::table('game_players')
            ->where('game_id', $game->id)
            ->where('user_id', $userId)
            ->exists();

        if ($alreadySignedUp) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'You are already signed up for this game. Remove yourself first if you want to change your role.'], 422);
            }
            return back()->withErrors(['gameRole' => 'You are already signed up for this game. Remove yourself first if you want to change your role.']);
        }

        // Enforce max 2 goalies per game
        if ($role === 'goalie') {
            $userGoalieCount = DB::table('game_players')->where('game_id', $game->id)->where('role', 'goalie')->count();
            $guestGoalieCount = DB::table('game_players_guests')->where('game_id', $game->id)->where('role', 'goalie')->count();
            $goalieCount = $userGoalieCount + $guestGoalieCount;
            if ($goalieCount >= 2) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => 'There are already two goalies for this game. Remove a goalie first.'], 422);
                }
                return back()->withErrors(['gameRole' => 'There are already two goalies for this game. Remove a goalie first.']);
            }
        }

        GamePlayer::create([
            'user_id' => $userId,
            'game_id' => $game->id,
            'role' => $role
        ]);

        // If the user previously marked 'cannot' for this game, remove that response now that they've accepted.
        DB::table('game_player_responses')
            ->where('game_id', $game->id)
            ->where('user_id', $userId)
            ->delete();

        $now = Carbon::now()->setTimezone('America/Toronto');
        (new GameTeamsService())->ensureLockedTeams($game, $now);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'You have joined the game!']);
        }

        return back()->with('success', 'You have successfully added your game!');
    }

    /**
     * Mark current user as cannot attend this game.
     */
    public function cannotAttend(Request $request, Game $game)
    {
        if (!Auth::check()) abort(403);

        $userId = Auth::id();

        // If user already signed up for this game, don't allow cannot-attend here
        $already = DB::table('game_players')->where('game_id', $game->id)->where('user_id', $userId)->exists();
        if ($already) {
            if ($request->expectsJson() || $request->ajax()) return response()->json(['error' => 'You are already attending this game. Remove yourself first.'], 422);
            return back()->withErrors(['cannotAttend' => 'You are already attending this game. Remove yourself first.']);
        }

        // Insert or update response to 'cannot'
        DB::table('game_player_responses')->updateOrInsert(
            ['game_id' => $game->id, 'user_id' => $userId],
            ['status' => 'cannot', 'updated_at' => now(), 'created_at' => now()]
        );

        if ($request->expectsJson() || $request->ajax()) return response()->json(['success' => true, 'message' => 'Marked as not attending']);
        return back()->with('success', 'Marked as not attending');
    }

    public function generateTeams() {
        
        Artisan::call('pp:generate-teams');

        return back()->with('success', 'You have successfully generated teams!');
    }

    public function updateGuest(UserAcceptGameRequestGuest $request, Game $game) {

        $guestName = trim((string) $request->input('guestName'));
        $guestName = preg_replace('/\s+/', ' ', $guestName) ?? $guestName;
        $guestName = Str::title(Str::lower($guestName));

        $role = (string) $request->input('gameRole');

        $level = $request->has('level') ? (int) $request->input('level') : 3;

        // Enforce max 2 goalies per game (users + guests)
        if ($role === 'goalie') {
            $userGoalieCount = DB::table('game_players')->where('game_id', $game->id)->where('role', 'goalie')->count();
            $guestGoalieCount = DB::table('game_players_guests')->where('game_id', $game->id)->where('role', 'goalie')->count();
            if (($userGoalieCount + $guestGoalieCount) >= 2) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => 'There are already two goalies for this game. Add them as a player instead.'], 422);
                }
                return back()->withErrors(['gameRole' => 'There are already two goalies for this game. Add them as a player instead.']);
            }
        }

        $alreadyAttending = GamePlayersGuest::where('game_id', $game->id)
            ->where('name', $guestName)
            ->exists();

        if ($alreadyAttending) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'That guest is already attending this game.'], 422);
            }
            return back()->withErrors(['guestName' => 'That guest is already attending this game.']);
        }

        try {
            GamePlayersGuest::create([
                'name' => $guestName,
                'game_id' => $game->id,
                'role' => $role,
                'level' => $level
            ]);
        } catch (QueryException $e) {
            // In case two requests race, the DB unique index will throw here.
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'That guest is already attending this game.'], 422);
            }
            return back()->withErrors(['guestName' => 'That guest is already attending this game.']);
        }

        $now = Carbon::now()->setTimezone('America/Toronto');
        (new GameTeamsService())->ensureLockedTeams($game, $now);
        
        
        if (!Guest::where('name', $guestName)->exists()){
            Guest::create([
                'name' => $guestName
            ]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Guest added successfully!']);
        }

        return back()->with('success', 'You have successfully added a guest to the game!');
    }

    public function searchGuestList(Request $request) {
        if ($request->ajax()) {
            $prefix = trim((string) $request->guestName);
            if ($prefix === '') return '';

            $data = Guest::where('name', 'LIKE', $prefix.'%')
                ->orderBy('name')
                ->limit(8)
                ->get();

            $output='';
            if (count($data) > 0) {
                $output .= '<ul class="py-1">';
                foreach ($data as $row) {
                    $name = e($row->name);
                    // Try to find the most recent saved level for this guest from past game guest entries
                    $levelRow = DB::table('game_players_guests')->where('name', $row->name)->orderByDesc('id')->limit(1)->first();
                    $level = $levelRow && isset($levelRow->level) ? (int) $levelRow->level : 3;
                    $output .= '<li data-level="'.e($level).'" class="px-3 py-2 text-slate-200 hover:bg-slate-800 cursor-pointer select-none">'.$name.'</li>';
                }
                $output .= '</ul>';
            } else {
                $output .= '';
            }
            return $output;
        }
    }

    public function adminUpdate(UserAcceptGameRequest $request, Game $game, $user_id) {
        $role = $request->input('gameRole');

        // If switching to goalie, enforce max 2 goalies (unless this user is already a goalie)
        if ($role === 'goalie') {
            $existing = DB::table('game_players')->where('game_id', $game->id)->where('user_id', $user_id);
            $userIsCurrentlyGoalie = $existing->exists() && $existing->first()->role === 'goalie';
            $userGoalieCount = DB::table('game_players')->where('game_id', $game->id)->where('role', 'goalie')->count();
            $guestGoalieCount = DB::table('game_players_guests')->where('game_id', $game->id)->where('role', 'goalie')->count();
            $goalieCount = $userGoalieCount + $guestGoalieCount;
            if (!$userIsCurrentlyGoalie && $goalieCount >= 2) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => 'There are already two goalies for this game. Remove a goalie first.'], 422);
                }
                return back()->withErrors(['gameRole' => 'There are already two goalies for this game. Remove a goalie first.']);
            }
        }

        // If a player record exists for this user/game, update the role. Otherwise insert.
        $existing = DB::table('game_players')->where('game_id', $game->id)->where('user_id', $user_id);
        if ($existing->exists()) {
            $updated = $existing->update(['role' => $role]);
        } else {
            $inserted = DB::table('game_players')->insert([
                'user_id' => $user_id,
                'game_id' => $game->id,
                'role' => $role
            ]);
            $updated = (bool) $inserted;
        }

        // Remove any cannot-attend response for this user now that they are marked attending
        DB::table('game_player_responses')
            ->where('game_id', $game->id)
            ->where('user_id', $user_id)
            ->delete();

        // Return JSON for AJAX requests, otherwise redirect back
        $now = Carbon::now()->setTimezone('America/Toronto');
        (new GameTeamsService())->ensureLockedTeams($game, $now);

        if ($request->expectsJson() || $request->ajax()) {
            if ($updated) return response()->json(['success' => true, 'message' => 'Player role updated']);
            return response()->json(['error' => 'Unable to update player role'], 422);
        }

        if ($updated) return back()->with('success', 'Player role updated');
        return back()->with('error', 'Unable to update player role');
    }

    /**
     * Admin: update a guest's role on a game (by guest id)
     */
    public function adminUpdateGuest(Request $request, Game $game, $guest_id = null)
    {
        \Log::info('adminUpdateGuest.payload', ['route_guest_id' => $guest_id, 'body' => $request->all()]);

        $guestId = $guest_id;
        if (empty($guestId) && $request->has('guestId')) {
            $guestId = $request->input('guestId');
        }

        $request->validate([
            'gameRole' => 'required|string',
            'level' => 'nullable|integer|min:1|max:5'
        ]);

        if (empty($guestId)) {
            return response()->json(['error' => 'guest id required'], 422);
        }

        $newRole = $request->input('gameRole');

        // Enforce max 2 goalies per game when changing a guest to goalie
        if ($newRole === 'goalie') {
            $existingGuest = DB::table('game_players_guests')->where('id', $guestId)->where('game_id', $game->id)->first();
            $guestIsCurrentlyGoalie = $existingGuest && $existingGuest->role === 'goalie';

            $userGoalieCount = DB::table('game_players')->where('game_id', $game->id)->where('role', 'goalie')->count();
            $guestGoalieCount = DB::table('game_players_guests')->where('game_id', $game->id)->where('role', 'goalie')->count();

            // If this guest isn't currently a goalie, switching to goalie would add +1.
            $projectedTotal = $userGoalieCount + $guestGoalieCount + ($guestIsCurrentlyGoalie ? 0 : 1);
            if ($projectedTotal > 2) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => 'There are already two goalies for this game. Remove a goalie first.'], 422);
                }
                return back()->withErrors(['gameRole' => 'There are already two goalies for this game. Remove a goalie first.']);
            }
        }

        $updateData = ['role' => $newRole];
        if ($request->has('level')) {
            $updateData['level'] = (int) $request->input('level');
        }

        $updated = DB::table('game_players_guests')
            ->where('id', $guestId)
            ->where('game_id', $game->id)
            ->update($updateData);

        if ($updated) {
            $now = Carbon::now()->setTimezone('America/Toronto');
            (new GameTeamsService())->ensureLockedTeams($game, $now);
            return response()->json(['success' => true, 'message' => 'Guest role updated']);
        }

        return response()->json(['error' => 'Unable to update guest role'], 422);
    }

    /**
     * Admin: remove a guest from a game
     */
    public function adminRemoveGuest(Request $request, Game $game, $guest_id = null)
    {
        \Log::info('adminRemoveGuest.payload', ['route_guest_id' => $guest_id, 'body' => $request->all()]);

        $guestId = $guest_id;
        if (empty($guestId) && $request->has('guestId')) {
            $guestId = $request->input('guestId');
        }

        if (empty($guestId)) {
            return response()->json(['error' => 'guest id required'], 422);
        }

        $result = DB::transaction(function () use ($guestId, $game) {
            $deleted = DB::table('game_players_guests')
                ->where('id', $guestId)
                ->where('game_id', $game->id)
                ->delete();

            // Also remove any team assignments for this guest
            DB::table('game_teams_guests')
                ->where('guest_id', $guestId)
                ->where('game_id', $game->id)
                ->delete();

            return (bool) $deleted;
        });

        if ($result) return response()->json(['success' => true, 'message' => 'Guest removed']);
        return response()->json(['error' => 'Unable to remove guest'], 422);
    }

    /**
     * Admin: remove a player (user) from a game
     */
    public function adminRemovePlayer(Request $request, Game $game, $user_id = null)
    {
        \Log::info('adminRemovePlayer.payload', ['route_user_id' => $user_id, 'body' => $request->all()]);

        $uid = $user_id;
        if (empty($uid) && $request->has('userId')) {
            $uid = $request->input('userId');
        }

        if (empty($uid)) {
            return response()->json(['error' => 'user id required'], 422);
        }

        $result = DB::transaction(function () use ($uid, $game) {
            $deleted = DB::table('game_players')
                ->where('user_id', $uid)
                ->where('game_id', $game->id)
                ->delete();

            // Also remove any team assignments for this user
            DB::table('game_teams_players')
                ->where('user_id', $uid)
                ->where('game_id', $game->id)
                ->delete();

            return (bool) $deleted;
        });

        if ($result) return response()->json(['success' => true, 'message' => 'Player removed']);
        return response()->json(['error' => 'Unable to remove player'], 422);
    }

    /**
     * Allow the current authenticated user to remove themselves from a game.
     */
    public function removeSelf(Request $request, Game $game)
    {
        if (!Auth::check()) abort(403);

        $userId = Auth::id();

        // Ensure the user is actually attending
        $attending = DB::table('game_players')->where('game_id', $game->id)->where('user_id', $userId)->exists();
        if (!$attending) {
            if ($request->expectsJson() || $request->ajax()) return response()->json(['error' => 'You are not attending this game'], 422);
            return back()->withErrors(['remove' => 'You are not attending this game']);
        }

        $result = DB::transaction(function () use ($userId, $game) {
            $deleted = DB::table('game_players')
                ->where('user_id', $userId)
                ->where('game_id', $game->id)
                ->delete();

            // Also remove any team assignments for this user
            DB::table('game_teams_players')
                ->where('user_id', $userId)
                ->where('game_id', $game->id)
                ->delete();

            return (bool) $deleted;
        });

        if ($result) {
            if ($request->expectsJson() || $request->ajax()) return response()->json(['success' => true, 'message' => 'You have removed yourself from the game']);
            return back()->with('success', 'You have removed yourself from the game');
        }

        if ($request->expectsJson() || $request->ajax()) return response()->json(['error' => 'Unable to remove you from the game'], 422);
        return back()->with('error', 'Unable to remove you from the game');
    }

    /**
     * Admin: move a team member (user or guest) to a specific team.
     * This is a manual override; automatic team assignment will not reshuffle existing members.
     */
    public function adminMoveTeamMember(Request $request, Game $game)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $data = $request->validate([
            'memberType' => 'required|string|in:user,guest',
            'memberId' => 'required|integer',
            'team' => 'required|integer|in:1,2',
        ]);

        $now = Carbon::now()->setTimezone('America/Toronto');
        $teamsRevealAt = $game->time->copy()->subMinutes(30);
        if ($now->lessThan($teamsRevealAt)) {
            return response()->json(['error' => 'Teams can be managed 30 minutes before puck drop.'], 422);
        }

        // Ensure baseline team rows + assignments exist before moving.
        (new GameTeamsService())->ensureLockedTeams($game, $now);

        $team = (int) $data['team'];
        $memberId = (int) $data['memberId'];

        if ($data['memberType'] === 'user') {
            $attending = DB::table('game_players')->where('game_id', $game->id)->where('user_id', $memberId)->exists();
            if (!$attending) {
                return response()->json(['error' => 'That user is not attending this game.'], 422);
            }

            DB::table('game_teams_players')->updateOrInsert(
                ['game_id' => $game->id, 'user_id' => $memberId],
                ['team' => $team]
            );
        } else {
            $attending = DB::table('game_players_guests')->where('game_id', $game->id)->where('id', $memberId)->exists();
            if (!$attending) {
                return response()->json(['error' => 'That guest is not attending this game.'], 422);
            }

            DB::table('game_teams_guests')->updateOrInsert(
                ['game_id' => $game->id, 'guest_id' => $memberId],
                ['team' => $team]
            );
        }

        return response()->json(['success' => true, 'message' => 'Team updated']);
    }

    /**
     * Admin: remove a team assignment for a member (user or guest) without removing attendance.
     */
    public function adminRemoveTeamAssignment(Request $request, Game $game)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $data = $request->validate([
            'memberType' => 'required|string|in:user,guest',
            'memberId' => 'required|integer',
        ]);

        $now = Carbon::now()->setTimezone('America/Toronto');
        $teamsRevealAt = $game->time->copy()->subMinutes(30);
        if ($now->lessThan($teamsRevealAt)) {
            return response()->json(['error' => 'Teams can be managed 30 minutes before puck drop.'], 422);
        }

        $memberId = (int) $data['memberId'];
        if ($data['memberType'] === 'user') {
            DB::table('game_teams_players')
                ->where('game_id', $game->id)
                ->where('user_id', $memberId)
                ->delete();
        } else {
            DB::table('game_teams_guests')
                ->where('game_id', $game->id)
                ->where('guest_id', $memberId)
                ->delete();
        }

        return response()->json(['success' => true, 'message' => 'Team assignment removed']);
    }

    public function payment(UserAcceptGamePayment $request, Game $game) {

        $game->collected_game_cost += $request['gamePayment'];
        $game->save();

        GamePayment::create([
            'user_id' => $request->user()->id,
            'game_id' => $game->id,
            'payment' => $request['gamePayment'],
            'method' => $request['paymentMethod']
        ]);

        return back()->with('success', 'You have successfully added your payment!');
    }

    public function adminPayment(UserAcceptGamePayment $request, Game $game, $player_id) {

        $game->collected_game_cost += $request['gamePayment'];
        $game->save();

        GamePayment::create([
            'user_id' => $player_id,
            'game_id' => $game->id,
            'payment' => $request['gamePayment'],
            'method' => $request['paymentMethod']
        ]);

        return back()->with('success', 'You have successfully added your payment!');
    }

    /**
     * Admin: update the score for a game (dark and light)
     */
    public function adminUpdateScore(Request $request, Game $game)
    {
        $data = $request->validate([
            'dark_score' => 'required|integer|min:0',
            'light_score' => 'required|integer|min:0'
        ]);

        $game->dark_score = $data['dark_score'];
        $game->light_score = $data['light_score'];
        $saved = $game->save();

        if ($request->expectsJson() || $request->ajax()) {
            if ($saved) return response()->json(['success' => true, 'dark_score' => $game->dark_score, 'light_score' => $game->light_score]);
            return response()->json(['error' => 'Unable to save scores'], 422);
        }

        if ($saved) return back()->with('success', 'Scores updated');
        return back()->with('error', 'Unable to save scores');
    }

    /**
     * Get structured team rosters, scores, and status for a game.
     */
    public function getTeamsData(Game $game): array
    {
        $currentTime = Carbon::now()->setTimezone('America/Toronto');
        $teamsRevealAt = $game->time->copy()->subMinutes(30);
        $teamsReady = $currentTime->greaterThanOrEqualTo($teamsRevealAt);

        $currentUserTeam = null;
        if (Auth::check() && $teamsReady) {
            $row = DB::table('game_teams_players')
                ->where('game_id', $game->id)
                ->where('user_id', Auth::id())
                ->first();
            if ($row && isset($row->team)) {
                $currentUserTeam = ((int) $row->team) === 1 ? 'Dark' : (((int) $row->team) === 2 ? 'Light' : null);
            }
        }

        if ($teamsReady) {
            (new GameTeamsService())->ensureLockedTeams($game, $currentTime);
        }

        $darkTeamUsers = $game->gameTeamsPlayers()->wherePivot('team', 1)->get();
        $lightTeamUsers = $game->gameTeamsPlayers()->wherePivot('team', 2)->get();

        $darkTeamGuests = DB::table('game_teams_guests')
            ->join('game_players_guests', 'game_teams_guests.guest_id', '=', 'game_players_guests.id')
            ->where('game_teams_guests.game_id', $game->id)
            ->where('game_teams_guests.team', 1)
            ->get(['game_players_guests.id', 'game_players_guests.name', 'game_players_guests.role', 'game_players_guests.level']);

        $lightTeamGuests = DB::table('game_teams_guests')
            ->join('game_players_guests', 'game_teams_guests.guest_id', '=', 'game_players_guests.id')
            ->where('game_teams_guests.game_id', $game->id)
            ->where('game_teams_guests.team', 2)
            ->get(['game_players_guests.id', 'game_players_guests.name', 'game_players_guests.role', 'game_players_guests.level']);

        $goalieUserIds = $game->gamePlayers()
            ->wherePivot('role', GameRoles::Goalie)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $buildOrderedTeamMembers = function ($teamUsers, $teamGuests) use ($goalieUserIds) {
            $goaliesFirst = collect();
            $skaters = collect();

            foreach ($teamUsers as $u) {
                $isGoalie = in_array((int) $u->id, $goalieUserIds, true);
                $isCurrentUser = Auth::check() && ((int) $u->id === (int) Auth::id());
                $item = [
                    'type' => 'user',
                    'id' => (int) $u->id,
                    'name' => $u->name,
                    'level' => $u->level ?? 3,
                    'is_goalie' => $isGoalie,
                    'is_empty_net' => false,
                    'is_current_user' => $isCurrentUser,
                ];
                if ($isGoalie) {
                    $goaliesFirst->push($item);
                } else {
                    $skaters->push($item);
                }
            }

            foreach ($teamGuests as $g) {
                $isGoalie = ($g->role === 'goalie');
                $item = [
                    'type' => 'guest',
                    'id' => (int) $g->id,
                    'name' => $g->name,
                    'level' => $g->level ?? 3,
                    'is_goalie' => $isGoalie,
                    'is_empty_net' => false,
                    'is_current_user' => false,
                ];
                if ($isGoalie) {
                    $goaliesFirst->push($item);
                } else {
                    $skaters->push($item);
                }
            }

            if ($goaliesFirst->isEmpty()) {
                $goaliesFirst->push([
                    'type' => 'empty',
                    'id' => null,
                    'name' => 'Empty Net',
                    'is_goalie' => true,
                    'is_empty_net' => true,
                    'is_current_user' => false,
                ]);
            }

            return $goaliesFirst->concat($skaters)->values();
        };

        $darkTeamMembers = $buildOrderedTeamMembers($darkTeamUsers, $darkTeamGuests);
        $lightTeamMembers = $buildOrderedTeamMembers($lightTeamUsers, $lightTeamGuests);

        $darkTeamSkill = 0;
        foreach ($darkTeamUsers as $u) {
            if (($u->role ?? null) === 'goalie') continue;
            $darkTeamSkill += (int) ($u->level ?? 3);
        }
        foreach ($darkTeamGuests as $g) {
            if (($g->role ?? null) === 'goalie') continue;
            $darkTeamSkill += (int) ($g->level ?? 3);
        }

        $lightTeamSkill = 0;
        foreach ($lightTeamUsers as $u) {
            if (($u->role ?? null) === 'goalie') continue;
            $lightTeamSkill += (int) ($u->level ?? 3);
        }
        foreach ($lightTeamGuests as $g) {
            if (($g->role ?? null) === 'goalie') continue;
            $lightTeamSkill += (int) ($g->level ?? 3);
        }

        return [
            'currentTime' => $currentTime,
            'teamsRevealAt' => $teamsRevealAt,
            'teamsReady' => $teamsReady,
            'currentUserTeam' => $currentUserTeam,
            'darkTeamMembers' => $darkTeamMembers,
            'lightTeamMembers' => $lightTeamMembers,
            'darkTeamSkill' => $darkTeamSkill,
            'lightTeamSkill' => $lightTeamSkill,
        ];
    }

    /**
     * Endpoint returning live roster status & rendered HTML at T-30.
     */
    public function teamsRoster(Request $request, Game $game)
    {
        $teamData = $this->getTeamsData($game);

        if (!$teamData['teamsReady']) {
            $now = Carbon::now()->setTimezone('America/Toronto');
            $secondsRemaining = max(0, $now->diffInSeconds($teamData['teamsRevealAt'], false));

            return response()->json([
                'ready' => false,
                'reveal_at' => $teamData['teamsRevealAt']->toIso8601String(),
                'reveal_timestamp' => $teamData['teamsRevealAt']->timestamp,
                'seconds_remaining' => $secondsRemaining,
            ]);
        }

        $html = view('components.teams_roster', array_merge($teamData, ['game' => $game]))->render();

        return response()->json([
            'ready' => true,
            'reveal_at' => $teamData['teamsRevealAt']->toIso8601String(),
            'reveal_timestamp' => $teamData['teamsRevealAt']->timestamp,
            'current_user_team' => $teamData['currentUserTeam'],
            'dark_team_skill' => $teamData['darkTeamSkill'],
            'light_team_skill' => $teamData['lightTeamSkill'],
            'html' => $html,
        ]);
    }

    /**
     * Download RFC 5545 iCalendar (.ics) with rink location and alarms at T-2h and T-30m.
     */
    public function downloadIcs(Game $game)
    {
        $startTime = $game->time->copy()->setTimezone('UTC');
        $duration = (int) ($game->duration ?: 60);
        $endTime = $startTime->copy()->addMinutes($duration);
        $nowTime = Carbon::now('UTC')->format('Ymd\THis\Z');

        $startFormatted = $startTime->format('Ymd\THis\Z');
        $endFormatted = $endTime->format('Ymd\THis\Z');

        $title = 'Pickup Puck: ' . ($game->title ?: 'Hockey Game');
        $location = $game->location ?: 'Hockey Arena';
        $url = route('game_detail.game_id', $game->id);
        $description = "Pickup Hockey League\\nLocation: {$location}\\nGame Details: {$url}\\n\\nNote: Teams reveal 30 minutes before puck drop!";

        $safeSummary = addcslashes($title, ",;\\");
        $safeLocation = addcslashes($location, ",;\\");
        $uid = "game-{$game->id}-{$game->time->timestamp}@pickuppuck.com";

        $ics = "BEGIN:VCALENDAR\r\n"
            . "VERSION:2.0\r\n"
            . "PRODID:-//Pickup Puck//Pickup Hockey League//EN\r\n"
            . "CALSCALE:GREGORIAN\r\n"
            . "METHOD:PUBLISH\r\n"
            . "BEGIN:VEVENT\r\n"
            . "UID:{$uid}\r\n"
            . "DTSTAMP:{$nowTime}\r\n"
            . "DTSTART:{$startFormatted}\r\n"
            . "DTEND:{$endFormatted}\r\n"
            . "SUMMARY:{$safeSummary}\r\n"
            . "DESCRIPTION:{$description}\r\n"
            . "LOCATION:{$safeLocation}\r\n"
            . "URL:{$url}\r\n"
            . "STATUS:CONFIRMED\r\n"
            . "BEGIN:VALARM\r\n"
            . "TRIGGER:-PT2H\r\n"
            . "ACTION:DISPLAY\r\n"
            . "DESCRIPTION:Pickup Puck Reminder: Puck drops in 2 hours at {$safeLocation}!\r\n"
            . "END:VALARM\r\n"
            . "BEGIN:VALARM\r\n"
            . "TRIGGER:-PT30M\r\n"
            . "ACTION:DISPLAY\r\n"
            . "DESCRIPTION:Pickup Puck: Teams are revealed! Check your team assignment at {$url}\r\n"
            . "END:VALARM\r\n"
            . "END:VEVENT\r\n"
            . "END:VCALENDAR\r\n";

        $fileName = 'pickup-puck-game-' . $game->id . '.ics';

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
        ]);
    }
}

