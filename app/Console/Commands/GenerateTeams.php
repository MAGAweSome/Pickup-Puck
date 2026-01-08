<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Games\Game;
use Carbon\Carbon;
use App\Services\GameTeamsService;

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

        $service = new GameTeamsService();

        foreach ($games as $game) {

            $service->ensureLockedTeams($game, $pointerTime);

        }
    }
}
