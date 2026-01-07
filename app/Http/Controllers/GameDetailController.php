<?php

namespace App\Http\Controllers;

use App\Enums\Games\GameRoles;
use App\Http\Requests\Admin\UserAcceptGamePayment;
use App\Http\Requests\Admin\UserAcceptGameRequest;
use App\Http\Requests\Admin\UserAcceptGameRequestGuest;
use App\Models\Games\Game;
use App\Models\Games\GamePayment;
use App\Models\Games\GamePlayer;
use App\Models\Games\GamePlayersGuest;
use App\Models\User;
use App\Models\Guest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GameDetailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
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

        $currentTime = Carbon::now();

        // return guest records so we have ids and names available for admin actions
        $guestPlayers = DB::table('game_players_guests')->where('game_id', $game->id)->where('role', 'player')->get();
        $guestGoalies = DB::table('game_players_guests')->where('game_id', $game->id)->where('role', 'goalie')->get();

        $lightTeamPlayers = $game->gameTeamsPlayers()->wherePivot('team', 1)->get()->pluck('name');
        $darkTeamPlayers = $game->gameTeamsPlayers()->wherePivot('team', 2)->get()->pluck('name');

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
            'lightTeamPlayers' => $lightTeamPlayers,
            'darkTeamPlayers' => $darkTeamPlayers,
            'currentTime' => $currentTime,
            'currentSeason' => $game->season,
            'guestPlayers' => $guestPlayers,
            'guestGoalies' => $guestGoalies
        ]);

    }

    public function update(UserAcceptGameRequest $request, Game $game) {
        $role = $request->input('gameRole');

        // Enforce max 2 goalies per game
        if ($role === 'goalie') {
            $goalieCount = DB::table('game_players')->where('game_id', $game->id)->where('role', 'goalie')->count();
            if ($goalieCount >= 2) {
                return back()->withErrors(['gameRole' => 'There are already two goalies for this game. Remove a goalie first.']);
            }
        }

        GamePlayer::create([
            'user_id' => $request->user()->id,
            'game_id' => $game->id,
            'role' => $role
        ]);

        return back()->with('success', 'You have successfully added your game!');
    }

    public function generateTeams() {
        
        Artisan::call('pp:generate-teams');

        return back()->with('success', 'You have successfully generated teams!');
    }

    public function updateGuest(UserAcceptGameRequestGuest $request, Game $game) {

        GamePlayersGuest::create([
            'name' => $request['guestName'],
            'game_id' => $game->id,
            'role' => $request['gameRole']
        ]);
        
        
        if (!Guest::where('name', $request['guestName'])->exists()){
            Guest::create([
                'name' => $request['guestName']
            ]);
        }

        return back()->with('success', 'You have successfully added a guest to the game!');
    }

    public function searchGuestList(Request $request) {
        if ($request->ajax()) {
            $data = Guest::where('name', 'LIKE', $request->guestName.'%')->get();

            $output='';
            if (count($data) > 0) {
                $output .= '<ul id="list">';
                
                foreach ($data as $row) {
                    $output .= '<li class="item text-dark">'.$row->name.'</li>';
                }

                $output .= '</ul>';
            } else { 
                $output .='';
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
            $goalieCount = DB::table('game_players')->where('game_id', $game->id)->where('role', 'goalie')->count();
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

        // Return JSON for AJAX requests, otherwise redirect back
        if ($request->expectsJson() || $request->ajax()) {
            if ($updated) return response()->json(['success' => true]);
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
            'gameRole' => 'required|string'
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

            // If this guest is not currently a goalie, then adding them would increase guestGoalieCount
            $projectedTotal = $userGoalieCount + $guestGoalieCount + ($guestIsCurrentlyGoalie ? 0 : 1);
            if ($projectedTotal >= 3) { // >=3 means already 2 or more, can't add
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => 'There are already two goalies for this game. Remove a goalie first.'], 422);
                }
                return back()->withErrors(['gameRole' => 'There are already two goalies for this game. Remove a goalie first.']);
            }
        }

        $updated = DB::table('game_players_guests')
            ->where('id', $guestId)
            ->where('game_id', $game->id)
            ->update(['role' => $newRole]);

        if ($updated) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 422);
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

        $deleted = DB::table('game_players_guests')
            ->where('id', $guestId)
            ->where('game_id', $game->id)
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 422);
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

        $deleted = DB::table('game_players')
            ->where('user_id', $uid)
            ->where('game_id', $game->id)
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 422);
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
}
