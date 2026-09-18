<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\GameDefault;
use App\Models\Season;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display settings form with saved defaults.
     */
    public function index()
    {
        $seasons = Season::withCount('games')->orderBy('season_number', 'desc')->get();
        $currentSeason = Season::orderBy('season_number', 'desc')->first();
        $nextSeasonNumber = ($seasons->max('season_number') ?? 0) + 1;

        $model = GameDefault::first();
        $defaults = [
            'time' => $model->default_time ?? null,
            'location' => $model->default_location ?? null,
            'duration' => $model->default_duration ?? null,
            'price' => $model->default_price ?? null,
            'season_id' => $model->default_season_id ?? null,
            'title_template' => $model->default_title_template ?? null,
            'auto_increment' => (bool) ($model->default_auto_increment ?? false),
            'next_number' => $model->default_next_number ?? 1,
        ];

        return view('settings.index', [
            'defaults' => $defaults,
            'seasons' => $seasons,
            'currentSeason' => $currentSeason,
            'nextSeasonNumber' => $nextSeasonNumber,
        ]);
    }

    /**
     * Delete an entire season, including its games and attached roster records if present.
     */
    public function deleteSeason(Request $request, Season $season)
    {
        $seasonNumber = $season->season_number;
        $gamesCount = $season->games()->count();

        // Delete all attached games (their child relations: players, teams, payments, etc. cascade delete)
        foreach ($season->games as $game) {
            $game->delete();
        }

        // If game_defaults references this season, clear default_season_id
        $gameDefault = GameDefault::first();
        if ($gameDefault && $gameDefault->default_season_id == $season->id) {
            $gameDefault->default_season_id = null;
            $gameDefault->save();
        }

        $season->delete();

        $nextSeasonNumber = (Season::max('season_number') ?? 0) + 1;
        $msg = $gamesCount > 0
            ? "Season {$seasonNumber} and all {$gamesCount} attached game(s) and roster contents were deleted successfully."
            : "Season {$seasonNumber} was deleted successfully.";

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'next_season_number' => $nextSeasonNumber,
            ]);
        }

        return Redirect::route('settings.index')->with('success', $msg);
    }

    /**
     * Save settings defaults.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'default_time' => 'nullable|date_format:H:i',
            'default_location' => 'nullable|string|max:255',
            'default_duration' => 'nullable|integer|min:1',
            'default_price' => 'nullable|numeric|min:0',
            'default_season_id' => 'nullable|integer|exists:seasons,id',
            'default_title_template' => 'nullable|string|max:255',
        ]);

        $model = GameDefault::first();
        if (! $model) {
            $model = new GameDefault();
        }
        $model->default_time = $data['default_time'] ?? null;
        $model->default_location = $data['default_location'] ?? null;
        $model->default_duration = $data['default_duration'] ?? null;
        $model->default_price = $data['default_price'] ?? null;
        $model->default_season_id = $data['default_season_id'] ?? null;
        $model->default_title_template = $data['default_title_template'] ?? null;
        $model->save();

        return Redirect::route('settings.index')->with('success', 'Settings saved');
    }
}