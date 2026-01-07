<?php

namespace App\Http\Controllers;

use App\Models\Games\Game;
use App\Models\Season;
use App\Models\GameDefault;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreateGameController extends Controller
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
        // Retrieve a list of seasons from your database, for example:
        $seasons = Season::all(); // Assuming you have a Season model.

        $nextSeasonNumber = Season::max('season_number') + 1;

        // fetch persistent defaults (first row)
        $defaultsModel = GameDefault::first();
        $defaults = [
            'time' => $defaultsModel ? ($defaultsModel->default_time instanceof \DateTime ? $defaultsModel->default_time->format('H:i:s') : $defaultsModel->default_time) : null,
            'location' => $defaultsModel->default_location ?? null,
            'duration' => $defaultsModel->default_duration ?? null,
            'price' => $defaultsModel->default_price ?? null,
            'season_id' => $defaultsModel->default_season_id ?? null,
            'title_template' => $defaultsModel->default_title_template ?? null,
        ];

        // Determine current season (latest season) to auto-select if no default set
        $currentSeason = Season::orderBy('season_number', 'desc')->first();
        $currentSeasonId = $currentSeason ? $currentSeason->id : null;

        // Compute a suggested title based on the template and existing games (within the selected/current season)
        $defaults['suggested_title'] = null;
        if ($defaultsModel && $defaultsModel->default_title_template) {
            $template = $defaultsModel->default_title_template;
            if (strpos($template, '{n}') !== false) {
                $parts = explode('{n}', $template);
                $prefix = $parts[0];
                $suffix = $parts[1] ?? '';

                $like = $prefix . '%' . $suffix;
                // Decide which season to use for title calculation: defaults season or current season
                $selectedSeasonId = $defaultsModel->default_season_id ?? $currentSeasonId;

                $query = DB::table('games')->where('title', 'like', $like);
                if ($selectedSeasonId) {
                    $query->where('season_id', $selectedSeasonId);
                }
                $candidates = $query->orderBy('id', 'desc')->limit(200)->pluck('title');

                $max = 0;
                $p = '/^' . preg_quote($prefix, '/') . '(\d+)' . preg_quote($suffix, '/') . '$/';
                foreach ($candidates as $t) {
                    if (preg_match($p, $t, $m)) {
                        $num = intval($m[1]);
                        if ($num > $max) $max = $num;
                    }
                }

                $next = $max + 1;
                $defaults['suggested_title'] = str_replace('{n}', $next, $template);
            } else {
                $defaults['suggested_title'] = $template;
            }
        }

        // Determine current season (latest season) to auto-select if no default set
        $currentSeason = Season::orderBy('season_number', 'desc')->first();
        $currentSeasonId = $currentSeason ? $currentSeason->id : null;

        return view('create_game', [
            'seasons' => $seasons,
            'nextSeasonNumber' => $nextSeasonNumber,
            'defaults' => $defaults,
            'currentSeasonId' => $currentSeasonId,
        ]);
    }

    public function create(Request $request)
    {
        $title = $request->input('title');
        $time = $request->input('date') . ' ' . $request->input('time');
        $location = $request->input('location');
        $duration = $request->input('duration');
        $price = $request->input('price');
        $currentTime = Carbon::now()->setTimezone('GMT-4');

        // Associate the game with the selected season
        $season_id = $request->input('season');

        // Load defaults to possibly generate title and increment counter
        $defaultsModel = GameDefault::first();

        $finalTitle = $title;
        if (empty($finalTitle) && $defaultsModel && $defaultsModel->default_title_template) {
            $template = $defaultsModel->default_title_template;

            if (strpos($template, '{n}') !== false) {
                $parts = explode('{n}', $template);
                $prefix = $parts[0];
                $suffix = $parts[1] ?? '';

                // Build a LIKE pattern to find candidate titles. Use wildcards around the number.
                $like = $prefix . '%' . $suffix;

                // Decide which season to use: requested season, default season, or latest season
                $currentSeason = Season::orderBy('season_number', 'desc')->first();
                $currentSeasonId = $currentSeason ? $currentSeason->id : null;
                $selectedSeasonId = $season_id ?? ($defaultsModel->default_season_id ?? $currentSeasonId);

                // Fetch recent matching titles and parse numeric suffixes to find the max.
                $query = DB::table('games')->where('title', 'like', $like);
                if ($selectedSeasonId) {
                    $query->where('season_id', $selectedSeasonId);
                }
                $candidates = $query->orderBy('id', 'desc')->limit(200)->pluck('title');

                $max = 0;
                $p = '/^' . preg_quote($prefix, '/') . '(\d+)' . preg_quote($suffix, '/') . '$/';
                foreach ($candidates as $t) {
                    if (preg_match($p, $t, $m)) {
                        $num = intval($m[1]);
                        if ($num > $max) $max = $num;
                    }
                }

                $next = $max + 1;
                $finalTitle = str_replace('{n}', $next, $template);
            } else {
                $finalTitle = $template;
            }
        }

        $data = [
            'title' => $finalTitle,
            'time' => $time,
            'location' => $location,
            'duration' => $duration,
            'price' => $price,
            'created_at' => $currentTime,
            'updated_at' => $currentTime,
            'season_id' => $season_id,
        ];

        DB::table('games')->insert($data);

        // We no longer rely on a stored next-number; the next number is derived from existing game titles.

        return redirect('home');
    }

    public function createSeason(Request $request)
    {
        // Validate and create the new season in your database.
        // You can use Eloquent or the DB facade to insert a new season record.

        // For example:
        $season = new Season();
        $season->season_number = $request->input('season_number');
        $season->save();

        // Redirect back to the game creation form.
        return redirect()->back();
    }
}
