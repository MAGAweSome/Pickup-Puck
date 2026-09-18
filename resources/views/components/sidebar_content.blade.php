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

@php
    $isHomeActive = request()->routeIs('home');
    $isGamesActive = request()->routeIs('games.*') || request()->routeIs('game_detail.*') || request()->is('game/*');
    $isProfileActive = request()->routeIs('profile*');
    $isPlayersActive = request()->routeIs('user_list*');
    $isSettingsActive = request()->routeIs('settings.*');
@endphp

<div class="mb-4">
    <!-- Stadium Next Game Mini Scoreboard -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-b from-slate-900/90 to-slate-950/90 border border-cyan-500/25 p-4 shadow-[0_4px_20px_rgba(0,0,0,0.5)] backdrop-blur-md">
        <div class="absolute -top-10 -right-10 w-24 h-24 bg-cyan-500/10 rounded-full blur-xl pointer-events-none"></div>

        <div class="flex items-center justify-between gap-2 mb-2.5">
            <span class="inline-flex items-center gap-1.5 text-[10px] font-mono font-bold tracking-widest text-cyan-400 uppercase">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                Next Puck Drop
            </span>
            @if($currentSeason)
                <span class="text-[10px] font-mono text-slate-400 bg-slate-800/80 px-2 py-0.5 rounded border border-slate-700/60">S{{ $currentSeason->season_number }}</span>
            @endif
        </div>

        @if($nextGame)
            <a href="{{ route('game_detail.game_id', ['game' => $nextGame->id]) }}" class="block text-base font-bold text-white hover:text-cyan-300 transition truncate no-underline">
                {{ $nextGame->title ?? 'Upcoming Game' }}
            </a>

            @if($nextGame->time)
                <div class="mt-1 text-xs text-slate-300 flex items-center gap-1.5">
                    <i class="fa-regular fa-clock text-cyan-400 text-[11px]"></i>
                    <span>{{ $nextGame->time->format('M j \@ g:ia') }}</span>
                </div>
            @endif

            <div class="mt-3 grid grid-cols-2 gap-2 pt-2 border-t border-slate-800/80 text-center">
                <div class="bg-slate-900/70 border border-slate-800 rounded-xl py-1.5 px-2">
                    <div class="text-[10px] uppercase font-semibold text-slate-400">Skaters</div>
                    <div class="text-base font-black font-mono text-cyan-300">{{ $nextGamePlayers }}</div>
                </div>
                <div class="bg-slate-900/70 border border-slate-800 rounded-xl py-1.5 px-2">
                    <div class="text-[10px] uppercase font-semibold text-slate-400">Goalies</div>
                    <div class="text-base font-black font-mono text-sky-400">{{ $nextGameGoalies }}</div>
                </div>
            </div>
        @else
            <div class="py-2 text-center text-xs text-slate-400">No upcoming games scheduled</div>
        @endif

        <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
            <span>Remaining Games</span>
            <span class="font-mono font-bold text-cyan-400">{{ $upcomingGames }}</span>
        </div>
    </div>
</div>

<!-- Navigation Links -->
<nav class="space-y-1.5 pt-2">
    @if(auth()->check())
        <a href="{{ route('home') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition duration-150 no-underline {{ $isHomeActive ? 'bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 shadow-[0_0_15px_rgba(56,189,248,0.12)]' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
            <i class="fa-solid fa-house w-4 text-center {{ $isHomeActive ? 'text-cyan-300' : 'text-slate-400' }}"></i>
            <span>Dashboard</span>
        </a>

        <a id="sidebarGamesLink" href="{{ route('games.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition duration-150 no-underline {{ $isGamesActive ? 'bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 shadow-[0_0_15px_rgba(56,189,248,0.12)]' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
            <i class="fa-solid fa-calendar-days w-4 text-center {{ $isGamesActive ? 'text-cyan-300' : 'text-slate-400' }}"></i>
            <span>Games Schedule</span>
        </a>

        <a href="{{ route('profile') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition duration-150 no-underline {{ $isProfileActive ? 'bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 shadow-[0_0_15px_rgba(56,189,248,0.12)]' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
            <i class="fa-solid fa-user w-4 text-center {{ $isProfileActive ? 'text-cyan-300' : 'text-slate-400' }}"></i>
            <span>My Profile</span>
        </a>

        @if(auth()->user()->hasRole('admin'))
            <div class="pt-3 mt-3 border-t border-slate-800/80">
                <div class="px-3 pb-1 text-[10px] font-mono uppercase tracking-widest text-slate-500 font-bold">Admin Controls</div>
            </div>

            <a href="{{ route('user_list') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition duration-150 no-underline {{ $isPlayersActive ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
                <i class="fa-solid fa-users w-4 text-center {{ $isPlayersActive ? 'text-amber-300' : 'text-slate-400' }}"></i>
                <span>Players Directory</span>
            </a>

            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition duration-150 no-underline {{ $isSettingsActive ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
                <i class="fa-solid fa-gear w-4 text-center {{ $isSettingsActive ? 'text-amber-300' : 'text-slate-400' }}"></i>
                <span>League Settings</span>
            </a>
        @endif
    @else
        <a href="{{ route('login') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 shadow-[0_0_15px_rgba(56,189,248,0.12)] transition duration-150 no-underline">
            <i class="fa-solid fa-right-to-bracket w-4 text-center text-cyan-300"></i>
            <span>Sign In / Register</span>
        </a>
    @endif
</nav>
