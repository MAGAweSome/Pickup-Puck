<?php

namespace App\Http\Controllers;

use App\Models\Games\Game;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserGameHistoryController extends Controller
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
    public function index(User $user)
    {
        $users = User::all();
        $games = Game::with(['gamePlayers'])->get();

        $gamesAttending = array();

        foreach ($games as $game) {
            $players = $game->players->pluck('name')->toArray();
            $goalies = $game->goalies->pluck('name')->toArray();

            if (in_array(Auth::user()->name, $players)) {
                array_push($gamesAttending, $game->id);
            }

            if (in_array(Auth::user()->name, $goalies)) {
                array_push($gamesAttending, $game->id);
            }
        }

        return view('user_game_history', [
            'games' => $games,
            'users' => $users,
            'gamesAttending' => $gamesAttending,
            'user' => $user,
        ]);
    }
}
