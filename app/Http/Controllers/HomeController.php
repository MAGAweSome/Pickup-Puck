<?php

namespace App\Http\Controllers;

use App\Enums\Games\GameRoles;
use App\Models\Games\Game;
use App\Models\Games\GamePlayer;
use App\Models\Season;
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

        $hasNotSignedUpForAllGames = false;

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

        $seasons = Season::all();
        $currentSeason = 0; // Initialize the currentSeason variable

        if ($seasons->isEmpty()) {
            $currentSeason = 0; // If there are no seasons, set currentSeason to 0
        } elseif ($seasons->count() === 1) {
            $currentSeason = 1; // If there is only one season, set currentSeason to 1
        } else {
            $currentSeason = 1; // Start with season 1

            foreach ($seasons as $season) {
                // Find the next game with a time in the future in the current season
                $nextGame = Game::where('season_id', $season->id)
                    ->where('time', '>', Carbon::now())
                    ->orderBy('time', 'asc')
                    ->first();

                if ($nextGame) {
                    // There is a game in this season with a time in the future, so we don't increment currentSeason.
                    break; // Exit the loop
                } else {
                    // All games in this season have passed.
                    $currentSeason++; // Increment currentSeason
                }
            }

            // If no future game was found in any season, set currentSeason back to 0
            if ($currentSeason > $seasons->count()) {
                $currentSeason = 0;
            }
        }

        $gameIds = Game::where('season_id', $currentSeason)->pluck('id')->toArray();

        foreach ($gameIds as $gameId) {
            // Check if there is a record in game_players matching the user and game IDs
            $exists = GamePlayer::where('user_id', Auth::user()->id)
                ->where('game_id', $gameId)
                ->exists();

            if (!$exists) {
                // If any game is not confirmed, set allGamesConfirmed to false
                $hasNotSignedUpForAllGames = true;
                break;
            }
        }

        // Change back to home...
        return view('home', [
            'users' => $users,
            'games' => $games,
            'currentTime' => $currentTime,
            'allGameTimesPassed' => $allGameTimesPassed,
            'gamesAttending' => $gamesAttending,
            'hasNotSignedUpForAllGames' => $hasNotSignedUpForAllGames
        ]);
    }

    public function acceptAllGamesInSeason(Season $season)
    {

        $seasons = Season::all();
        $currentSeason = 0; // Initialize the currentSeason variable

        if ($seasons->isEmpty()) {
            $currentSeason = 0; // If there are no seasons, set currentSeason to 0
        } elseif ($seasons->count() === 1) {
            $currentSeason = 1; // If there is only one season, set currentSeason to 1
        } else {
            $currentSeason = 1; // Start with season 1

            foreach ($seasons as $season) {
                // Find the next game with a time in the future in the current season
                $nextGame = Game::where('season_id', $season->id)
                    ->where('time', '>', Carbon::now())
                    ->orderBy('time', 'asc')
                    ->first();

                if ($nextGame) {
                    // There is a game in this season with a time in the future, so we don't increment currentSeason.
                    break; // Exit the loop
                } else {
                    // All games in this season have passed.
                    $currentSeason++; // Increment currentSeason
                }
            }

            // If no future game was found in any season, set currentSeason back to 0
            if ($currentSeason > $seasons->count()) {
                $currentSeason = 0;
            }
        }

        // Get all games for the specified season
        $seasonGames = Game::where('season_id', $currentSeason)->get();

        foreach ($seasonGames as $seasonGame) {
            if ($seasonGame->time > Carbon::now()) {
                // Check if the player has already signed up for the game
                $exists = GamePlayer::where('user_id', Auth::user()->id)
                    ->where('game_id', $seasonGame->id)
                    ->exists();

                // If the game is in the future, and has not signed up yet, create a GamePlayer record
                if (!$exists) {
                    GamePlayer::create([
                        'user_id' => Auth::user()->id,
                        'game_id' => $seasonGame->id,
                        'role' => Auth::user()->role_preference
                    ]);
                }
            }
        }

        // Redirect back to the season or a relevant page
        return back()->with('success', 'You have successfully signed up for the season!');
    }
}
