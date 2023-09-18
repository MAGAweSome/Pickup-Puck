<?php

namespace App\Http\Controllers;

use App\Models\Games\Game;
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
        return view('create_game');
    }

    public function create(Request $request)
    {
        $title = $request->input('title');
        // $description = $request->input('description');
        $time = $request->input('date') . ' ' . $request->input('time');        
        $location = $request->input('location');
        $duration = $request->input('duration');
        $price = $request->input('price');
        $ice_cost = $request->input('ice_cost');
        $currentTime = Carbon::now()->setTimezone('GMT-4');

        $data = array('title' => $title, 'time' => $time, 'location' => $location, 'duration' => $duration, 'price' => $price, 'ice_cost' => $ice_cost, 'created_at' => $currentTime, 'updated_at' => $currentTime);
        
        DB::table('games')->insert($data);

        return redirect('home');
    }
}
