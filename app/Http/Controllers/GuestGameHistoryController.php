<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Games\Game;
use Illuminate\Support\Facades\DB;

class GuestGameHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:admin']);
    }

    public function index(Guest $guest)
    {
        $rows = DB::table('game_players_guests')
            ->where('name', $guest->name)
            ->get(['game_id', 'role']);

        $roleByGameId = $rows
            ->mapWithKeys(fn ($r) => [(int) $r->game_id => (string) $r->role])
            ->all();

        $gameIds = $rows->pluck('game_id')->map(fn ($id) => (int) $id)->unique()->values();

        $games = Game::query()
            ->with(['season'])
            ->whereIn('id', $gameIds)
            ->orderByDesc('time')
            ->get();

        return view('guest_game_history', [
            'guest' => $guest,
            'games' => $games,
            'roleByGameId' => $roleByGameId,
        ]);
    }
}
