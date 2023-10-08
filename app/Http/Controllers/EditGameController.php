<?php

namespace App\Http\Controllers;

use App\Models\Games\Game;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EditGameController extends Controller
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

        $game_date = substr($game->time, 0, 10);
        $game_time = substr($game->time, 11, 5);

        return view('edit_game', ['game' => $game, 'game_date' => $game_date, 'game_time' => $game_time]);
    }

    public function update(Request $request, Game $game) {

        $game->title = $request['title'];
        $game->description = $request['description'];
        $game->time =  $request['date'] . " " . $request['time'];
        $game->location =$request['location'];
        $game->duration = $request['duration'];
        $game->price = $request['price'];
        // $game->ice_cost = $request['ice_cost'];
        
        $game->save();
        
        return redirect('home');
    }
    
    public function delete(Request $request, Game $game) {
        
        DB::table('games')->where('id', $game->id)->delete();
        
        return redirect('home')->with('success','Game deleted successfully!');;
    }
}
