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
        $goalies = $game->goalies->pluck('name');
        $user_is_a_goalie = False;
        $users = User::all();
        $guests = Guest::all();

        $currentTime = Carbon::now();

        $guestPlayers = DB::table('game_players_guests')->where('game_id', $game->id)->where('role', 'player')->get()->pluck('name');
        $guestGoalies = DB::table('game_players_guests')->where('game_id', $game->id)->where('role', 'goalie')->get()->pluck('name');

        $lightTeamPlayers = $game->gameTeamsPlayers()->wherePivot('team', 1)->get()->pluck('name');
        $darkTeamPlayers = $game->gameTeamsPlayers()->wherePivot('team', 2)->get()->pluck('name');

        $players_attending = array();

        foreach ($players as $player){
            array_push($players_attending, $player);
        }
        
        foreach ($goalies as $goalie){
            array_push($players_attending, $goalie);

            if (Auth::user()->name == $goalie){
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
            'guestPlayers' => $guestPlayers,
            'guestGoalies' => $guestGoalies
        ]);
    }

    public function update(UserAcceptGameRequest $request, Game $game) {
        
        GamePlayer::create([
            'user_id' => $request->user()->id,
            'game_id' => $game->id,
            'role' => $request['gameRole']
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
        
        GamePlayer::create([
            'user_id' => $user_id,
            'game_id' => $game->id,
            'role' => $request['gameRole']
        ]);

        return back()->with('success', 'You have successfully added your game!');
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
}
