<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Games\Game;
use App\Models\Games\GameTeamsPlayer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pp:generate-teams';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate / update teams for games within the next 30 min.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $pointerTime = Carbon::now()->setTimezone('America/Toronto');
        $games = Game::with(['gamePlayers', 'gameTeamsPlayers'])->whereBetween('time', [$pointerTime, $pointerTime->copy()->addMinutes(30)])->get();

        foreach ($games as $game) {

            if ($game->gameTeamsPlayers->count() == 0) {
                $teams = $game->players->pluck('id')->shuffle()->chunk(ceil($game->players->count()/2))->toArray();
                
                foreach ($teams as $team_id => $team_players) {
                    foreach ($team_players as $player) {
                        GameTeamsPlayer::create([
                            'game_id' => $game->id,
                            'user_id' => $player,
                            'team' => $team_id + 1
                        ]);
                    }
                }
            } else {
                $assignedPlayers = $game->gameTeamsPlayers->pluck('pivot.user_id');

                foreach ($game->players->pluck('id')->diff($assignedPlayers)->shuffle()->toArray() as $player) {
                    GameTeamsPlayer::create([
                        'game_id' => $game->id,
                        'user_id' => $player,
                        'team' => DB::table('game_teams_players')->select(DB::raw('`team`, COUNT(*)'))->where('game_id', $game->id)->groupBy('team')->orderBy(DB::raw('COUNT(*)'))->first()->team
                    ]);
                }
            }

        }
    }
}
