<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\User;
use Illuminate\Http\Request;

class UserListController extends Controller
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
        $users = User::query()->orderBy('name')->get();
        
        // Fetch guests and attach their assigned skill level from recent games
        $guestLevels = \Illuminate\Support\Facades\DB::table('game_players_guests')
            ->select('name', \Illuminate\Support\Facades\DB::raw('MAX(level) as level'))
            ->groupBy('name')
            ->pluck('level', 'name');

        $guests = Guest::query()->orderBy('name')->get()->map(function ($guest) use ($guestLevels) {
            $guest->level = (int) ($guestLevels[$guest->name] ?? 3);
            return $guest;
        });

        return view('user_list', ['users' => $users, 'guests' => $guests]);
    }
}
