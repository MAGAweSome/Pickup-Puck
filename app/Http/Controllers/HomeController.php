<?php

namespace App\Http\Controllers;

use App\Enums\Games\GameRoles;
use App\Models\Games\Game;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rawilk\Settings\Facades\Settings;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HomeController extends Controller
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
    public function index()
    {      
        $users = User::all();
        $games = Game::with(['gamePlayers'])->get();

        $currentTime = Carbon::now()->setTimezone('America/Toronto');

        $allGameTimes = DB::table('games')->pluck('time');
        $allGameTimesPassed = $allGameTimes->isEmpty(); // Set $allGameTimesPassed to true if there are no game times

        $gamesAttending = array();

        if (!$allGameTimesPassed) {
            foreach ($allGameTimes as $item) {
                if ($item > $currentTime) {
                    $allGameTimesPassed = false;
                    break;
                }
            }
        }

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

        Settings::set('foo', 'Hello');

        // $role = Role::create(['name' => 'admin']);
        // Auth::user()->assignRole('admin');

        // Change back to home...
        return view('home', [
            'users' => $users,
            'games' => $games,
            'currentTime' => $currentTime,
            'allGameTimesPassed' => $allGameTimesPassed,
            'gamesAttending' => $gamesAttending
        ]);
    }
}
