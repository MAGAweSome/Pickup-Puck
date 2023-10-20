<?php

namespace App\Http\Controllers;

use App\Models\Games\Game;
use App\Models\Season;
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

        return view('create_game', [
            'seasons' => $seasons,
            'nextSeasonNumber' => $nextSeasonNumber
        ]);
    }

    public function create(Request $request)
    {
        $title = $request->input('title');
        // $description = $request->input('description');
        $time = $request->input('date') . ' ' . $request->input('time');
        $location = $request->input('location');
        $duration = $request->input('duration');
        $price = $request->input('price');
        // $ice_cost = $request->input('ice_cost');
        $currentTime = Carbon::now()->setTimezone('GMT-4');

        // Associate the game with the selected season
        $season_id = $request->input('season');

        $data = array(
            'title' => $title,
            'time' => $time,
            'location' => $location,
            'duration' => $duration,
            'price' => $price,
            'created_at' => $currentTime,
            'updated_at' => $currentTime,
            'season_id' => $season_id
        );

        DB::table('games')->insert($data);

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
