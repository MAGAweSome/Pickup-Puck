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
        $seasons = Season::all();
        $nextSeasonNumber = Season::max('season_number') + 1;

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

        return view('settings.index', ['defaults' => $defaults, 'seasons' => $seasons, 'nextSeasonNumber' => $nextSeasonNumber]);
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