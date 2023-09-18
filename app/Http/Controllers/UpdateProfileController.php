<?php

namespace App\Http\Controllers;

use App\Enums\Games\GameRoles;
use Illuminate\Http\Request;

class UpdateProfileController extends Controller
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
        return view('update_profile', ['GAME_ROLES' => GameRoles::cases()]);
    }
}
