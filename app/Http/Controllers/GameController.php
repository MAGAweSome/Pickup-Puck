<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Display a listing of games for the current season.
     */
    public function index(Request $request)
    {
        // Load all seasons for dropdown
        $seasons = Season::orderBy('season_number', 'desc')->get();

        // Determine selected season from query or default to latest
        $selectedSeasonId = $request->query('season');
        if ($selectedSeasonId) {
            $currentSeason = Season::find($selectedSeasonId);
        } else {
            $currentSeason = Season::orderBy('season_number', 'desc')->first();
            if (! $currentSeason) {
                $currentSeason = Season::latest('id')->first();
            }
        }

        $games = collect();
        if ($currentSeason) {
            $games = $currentSeason->games()->orderBy('time', 'asc')->get();
        }

        return view('games.index', compact('games', 'currentSeason', 'seasons'));
    }
}
