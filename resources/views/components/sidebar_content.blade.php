@php
    use App\Models\Season;
    use App\Models\Games\Game;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Carbon;

    $currentSeason = Season::orderBy('season_number', 'desc')->first();
    if (! $currentSeason) {
        $currentSeason = Season::latest('id')->first();
    }

    $upcomingGames = 0;
    $playersSigned = 0;
    if ($currentSeason) {
        $now = Carbon::now();
        $upcomingGames = $currentSeason->games()->where('time', '>=', $now)->count();

        // Count distinct users signed up for any game in the current season
        $playersSigned = DB::table('game_players')
            ->join('games', 'game_players.game_id', '=', 'games.id')
            ->where('games.season_id', $currentSeason->id)
            ->distinct()
            ->count('game_players.user_id');
    }

    // Prefer shared values provided by AppServiceProvider; fallback to local computation
    $nextGame = $sidebarNextGame ?? null;
    $upcomingCount = $sidebarUpcomingCount ?? null;
    $nextGamePlayers = 0;
    $nextGameGoalies = 0;
    $nextGameGuestPlayers = 0;
    $nextGameGuestGoalies = 0;
    if (is_null($nextGame) && class_exists(Game::class)) {
        try {
            $now = Carbon::now()->setTimezone('America/Toronto');
            $upcomingCount = Game::where('time', '>', $now)->count();
            $nextGame = Game::where('time', '>', $now)->orderBy('time', 'asc')->first();
        } catch (\Exception $e) {
            $nextGame = null;
        }
    }
    if ($nextGame) {
        // `players` and `goalies` are attribute accessors returning collections (registered users)
        $nextGamePlayers = is_countable($nextGame->players) ? $nextGame->players->count() : 0;
        $nextGameGoalies = is_countable($nextGame->goalies) ? $nextGame->goalies->count() : 0;

        // Guests (match GameDetailController / Quick Info logic)
        try {
            $nextGameGuestPlayers = DB::table('game_players_guests')
                ->where('game_id', $nextGame->id)
                ->where('role', 'player')
                ->count();
            $nextGameGuestGoalies = DB::table('game_players_guests')
                ->where('game_id', $nextGame->id)
                ->where('role', 'goalie')
                ->count();
        } catch (\Exception $e) {
            $nextGameGuestPlayers = 0;
            $nextGameGuestGoalies = 0;
        }

        // Total counts (users + guests)
        $nextGamePlayers += $nextGameGuestPlayers;
        $nextGameGoalies += $nextGameGuestGoalies;
    }
@endphp

<div class="mb-4 px-3">
    <h2 class="text-2xl font-semibold text-ice-blue text-center">Pickup Puck</h2>
    <p class="text-sm text-slate-300 text-center">Next Game Info</p>

    <div class="mt-3">
        @if($nextGame)
            <a href="{{ route('game_detail.game_id', ['game' => $nextGame->id]) }}" class="block text-lg font-semibold text-ice-blue truncate text-center no-underline hover:text-ice-blue">{{ $nextGame->title ?? 'Upcoming Game' }}</a>

            @if($nextGame->time)
                <div class="mt-1 text-sm text-slate-300 text-center">{{ $nextGame->time->format('M j \@ g:ia') }}</div>
            @endif

            <div class="mt-3 grid grid-cols-2 gap-2 text-center">
                <div>
                    <div class="text-xs text-slate-400">Players</div>
                    <div class="text-lg font-semibold text-ice-blue">{{ $nextGamePlayers }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-400">Goalies</div>
                    <div class="text-lg font-semibold text-ice-blue">{{ $nextGameGoalies }}</div>
                </div>
            </div>
        @endif

        <div class="mt-4">
            <div class="text-sm text-slate-300 text-center">Season Stats</div>
            <div class="mt-2 grid grid-cols-2 gap-2 text-center">
                <div>
                    <div class="text-xs text-slate-400">Season</div>
                    <div class="text-lg font-semibold text-ice-blue">{{ $currentSeason ? $currentSeason->season_number : '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-400">Games Left</div>
                    <div class="text-lg font-semibold text-ice-blue">{{ $upcomingGames }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<nav class="space-y-2 px-2 pt-4 border-t border-slate-700">
    @if(auth()->check())
        <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-700 text-slate-100 hover:text-slate-100 border-transparent shadow-none ring-0 no-underline">
            <i class="fa-solid fa-house text-ice-blue w-4"></i>
            <span>Dashboard</span>
        </a>
        <a id="sidebarGamesLink" href="{{ route('games.index') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-700 text-slate-100 hover:text-slate-100 border-transparent shadow-none ring-0 no-underline">
            <i class="fa-solid fa-calendar-days text-ice-blue w-4"></i>
            <span>Games</span>
        </a>
        <a href="{{ route('profile') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-700 text-slate-100 hover:text-slate-100 border-transparent shadow-none ring-0 no-underline">
            <i class="fa-solid fa-user text-ice-blue w-4"></i>
            <span>Profile</span>
        </a>

        @if(auth()->user()->hasRole('admin'))
            <div class="pt-2 mt-2 border-t border-slate-700"></div>
            <a href="{{ route('user_list') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-700 text-slate-100 hover:text-slate-100 border-transparent shadow-none ring-0 no-underline">
                <i class="fa-solid fa-users text-ice-blue w-4"></i>
                <span>Players</span>
            </a>
            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-700 text-slate-100 hover:text-slate-100 border-transparent shadow-none ring-0 no-underline">
                <i class="fa-solid fa-gear text-ice-blue w-4"></i>
                <span>Settings</span>
            </a>
        @endif
    @else
        <a href="{{ route('login') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-700 text-slate-100 hover:text-slate-100 border-transparent shadow-none ring-0 no-underline">
            <i class="fa-solid fa-right-to-bracket text-ice-blue w-4"></i>
            <span>Login / Register</span>
        </a>
    @endif
</nav>
