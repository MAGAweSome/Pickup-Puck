@extends('layouts.app')

@section('content')

    @php
        $isOnboarding = request()->boolean('onboarding');

        $currentSeason = (isset($currentSeason) && $currentSeason)
            ? $currentSeason
            : ((\App\Models\GameDefault::first()?->default_season_id ? \App\Models\Season::find(\App\Models\GameDefault::first()->default_season_id) : null)
                ?? \App\Models\Season::orderBy('season_number', 'desc')->first());

        $upcomingGames = isset($games)
            ? $games->filter(function($g) use ($currentTime, $currentSeason) {
                if ($g->time <= $currentTime) return false;
                return $currentSeason ? ($g->season_id == $currentSeason->id) : true;
            })->values()
            : collect();

        $pastGames = isset($games)
            ? $games->filter(function($g) use ($currentTime, $currentSeason) {
                if ($g->time >= $currentTime) return false;
                return $currentSeason ? ($g->season_id == $currentSeason->id) : true;
            })->values()
            : collect();

        $nextGame = $upcomingGames->first();
        $remainingUpcoming = $upcomingGames->slice(1);
    @endphp

    <div class="space-y-8 w-full">
        <!-- Top Command Welcome Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 p-6 sm:p-8 shadow-sm dark:shadow-md transition-colors">
            <!-- Stadium Accent Glow in Dark Mode / Ice Gradient in Light Mode -->
            <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-sky-500/10 dark:bg-sky-500/5 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono font-bold bg-sky-50 dark:bg-sky-500/15 border border-sky-200 dark:border-sky-500/30 text-sky-700 dark:text-sky-300 mb-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span>
                        <span>Game Day Command Hub</span>
                        @if($currentSeason)
                            <span>• Season {{ $currentSeason->season_number }}</span>
                        @endif
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-slate-100">
                        Welcome back, <span class="text-sky-600 dark:text-sky-400">{{ Auth::user()->name }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1">
                        Balanced snake rosters, live goalie crease tracking, and instant game-day reveals.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @role ('admin')
                        <a href="/admin/create_game" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-500 dark:bg-sky-500 dark:hover:bg-sky-400 text-white dark:text-slate-950 font-black text-xs sm:text-sm rounded-xl transition shadow-sm no-underline">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>Create Game</span>
                        </a>
                    @endrole
                    @if ($hasNotSignedUpForAllGames && isset($games) && $games->count())
                        <a href="{{ route('seasons.accept-all', ['season' => $games->first()->season_id]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-[#0f172a] hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-sky-300 font-bold text-xs sm:text-sm rounded-xl transition no-underline shadow-sm">
                            <i class="fa-solid fa-check-double text-xs"></i>
                            <span>Accept All Season Games</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- 12-Column Modular Grid: Main Action Stream (8 cols) + Season Pulse Station (4 cols) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
            
            <!-- Primary Stream (8 cols on desktop, responsive up to 3xl) -->
            <div class="col-span-12 lg:col-span-8 2xl:col-span-8 3xl:col-span-9 space-y-6">

                <!-- Featured Next Game Hero Card (if exists) -->
                @if($nextGame)
                    @php
                        $teamsRevealAt = $nextGame->time->copy()->subMinutes(30);
                        $isTeamsReady = \Carbon\Carbon::now()->setTimezone('America/Toronto')->greaterThanOrEqualTo($teamsRevealAt);
                        $isAttendingNext = in_array($nextGame->id, $gamesAttending);
                        $isDeclinedNext = isset($gamesDeclined) && in_array($nextGame->id, $gamesDeclined);
                    @endphp
                    <div class="relative z-20 rounded-3xl bg-white dark:bg-[#1e293b] border-2 border-sky-400/40 dark:border-sky-500/40 p-6 sm:p-7 shadow-sm dark:shadow-md transition-all">
                        <!-- Top Ribbon -->
                        <div class="flex items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-black uppercase tracking-wider bg-sky-500 text-white shadow-sm">
                                    <i class="fa-solid fa-hockey-puck text-xs"></i>
                                    Next Puck Drop
                                </span>
                                @if($isTeamsReady)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-mono font-bold text-emerald-800 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-500/20 px-3 py-0.5 rounded-full border border-emerald-300 dark:border-emerald-400/30">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Teams Revealed!
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-mono font-semibold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 rounded-full border border-slate-200 dark:border-slate-700">
                                        <i class="fa-regular fa-clock text-xs"></i>
                                        Reveals 30m Prior
                                    </span>
                                @endif
                            </div>

                            <div>
                                @if($isAttendingNext)
                                    @include('components.badge', ['status' => 'Attending'])
                                @elseif($isDeclinedNext)
                                    @include('components.badge', ['status' => 'Not Attending'])
                                @else
                                    @include('components.badge', ['status' => 'Not Yet Attending'])
                                @endif
                            </div>
                        </div>

                        <!-- Next Game Details -->
                        <div class="py-5 space-y-4">
                            <div>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ $nextGame->title }}
                                </h2>
                                <div class="mt-2 flex items-center gap-4 text-xs sm:text-sm text-slate-600 dark:text-slate-300 flex-wrap font-medium">
                                    <span class="flex items-center gap-1.5 text-sky-600 dark:text-sky-400 font-semibold">
                                        <i class="fa-regular fa-calendar-days"></i>
                                        <span>{{ $nextGame->game_time }}</span>
                                    </span>
                                    <span>•</span>
                                    <span class="flex items-center gap-1.5 text-rose-600 dark:text-rose-400">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <span>{{ $nextGame->location }}</span>
                                    </span>
                                    @if(auth()->check() && auth()->user()->role_preference !== \App\Enums\Games\GameRoles::Goalie->value)
                                        <span>•</span>
                                        <span class="font-mono font-bold text-slate-900 dark:text-white">${{ $nextGame->price }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Capacity / Attendance Bars -->
                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700/60">
                                    <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 font-semibold mb-1.5">
                                        <span>Skaters Signed Up</span>
                                        <span class="font-mono font-black text-slate-900 dark:text-slate-100">{{ $nextGame->players->count() }}</span>
                                    </div>
                                    <div class="h-2 w-full bg-slate-200 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-sky-500 rounded-full" style="width: {{ min(100, ($nextGame->players->count() / 20) * 100) }}%"></div>
                                    </div>
                                </div>

                                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700/60">
                                    <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 font-semibold mb-1.5">
                                        <span>Goalies</span>
                                        <span class="font-mono font-black text-slate-900 dark:text-slate-100">{{ $nextGame->goalies->count() }} / 2</span>
                                    </div>
                                    <div class="h-2 w-full bg-slate-200 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-cyan-500 dark:bg-cyan-400 rounded-full" style="width: {{ min(100, ($nextGame->goalies->count() / 2) * 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Next Game Action Footers -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 relative z-30">
                            <div class="flex items-center gap-2">
                                @include('components.add-to-calendar', ['game' => $nextGame])
                            </div>

                            <a href="/game/{{ $nextGame->id }}"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-500 dark:bg-sky-500 dark:hover:bg-sky-400 text-white dark:text-slate-950 font-black text-xs sm:text-sm rounded-xl transition no-underline shadow-sm">
                                <span>Locker Room &amp; Rosters</span>
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Tour article for Onboarding (preserves #gameCard, #gameLocation_Players, #gameMoreDetails) -->
                @if($isOnboarding)
                    <div class="bg-sky-50 dark:bg-slate-900 border border-sky-300 dark:border-cyan-500/30 rounded-2xl p-4 flex items-center justify-between gap-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">🏒</span>
                            <div>
                                <span class="font-bold text-sky-800 dark:text-cyan-300 text-xs uppercase tracking-wider">Interactive Tour Active</span>
                                <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">Learn how Pickup Puck games, team balancing, and rosters operate.</p>
                            </div>
                        </div>
                        <a href="{{ route('home') }}" class="px-3 py-1.5 text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-lg transition shrink-0 no-underline">
                            Dismiss Tour
                        </a>
                    </div>

                    <article id="gameCard" class="relative overflow-hidden rounded-3xl bg-white dark:bg-[#1e293b] border-2 border-sky-400 dark:border-sky-500/40 p-5 shadow-sm dark:shadow-md">
                        <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-sky-100 dark:bg-sky-500/20 text-sky-800 dark:text-sky-300">Onboarding Example</span>
                                <h2 class="text-lg font-black text-slate-900 dark:text-slate-100 mt-1">Example Pickup Game</h2>
                            </div>
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300">
                                    <i class="fa-solid fa-check text-xs"></i> Attending
                                </span>
                            </div>
                        </div>

                        <div class="py-3 text-xs text-slate-600 dark:text-slate-300">
                            Fri 9:30 PM • 60 min • $20
                        </div>

                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                            <div id="gameLocation_Players" class="text-slate-600 dark:text-slate-400">123 Example Arena • 10 Skaters • 2 Goalies</div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('home') }}" class="px-3 py-1 border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-lg transition no-underline">Skip</a>
                                <a id="gameMoreDetails" href="{{ route('games.index', ['onboarding' => 1]) }}" class="px-3 py-1 bg-sky-500 text-white dark:text-slate-950 font-bold rounded-lg text-xs no-underline hover:bg-sky-400 transition">See details &rarr;</a>
                            </div>
                        </div>
                    </article>
                @endif

                <!-- Remaining Upcoming Games Grid -->
                <section class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-2.5 h-2.5 rounded-full bg-sky-500 shadow-sm"></div>
                            <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-slate-100 tracking-wide">Upcoming Schedule</h2>
                        </div>
                        <a href="{{ route('games.index') }}" class="text-xs font-bold text-sky-600 dark:text-sky-400 hover:underline no-underline">
                            View Full Season &rarr;
                        </a>
                    </div>

                    @if($remainingUpcoming->isNotEmpty())
                        <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-3">
                            @foreach ($remainingUpcoming as $game)
                                <article class="rounded-2xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 hover:border-sky-400 dark:hover:border-sky-400/50 transition duration-150 p-5 shadow-sm dark:shadow-md flex flex-col justify-between group">
                                    <div class="space-y-2.5">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0">
                                                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition truncate">
                                                    {{ $game->title }}
                                                </h3>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1.5">
                                                    <i class="fa-regular fa-calendar text-sky-500"></i>
                                                    <span>{{ $game->game_time }}</span>
                                                </p>
                                            </div>

                                            <div class="shrink-0 text-right">
                                                @if(in_array($game->id, $gamesAttending))
                                                    @include('components.badge', ['status' => 'Attending'])
                                                @elseif(isset($gamesDeclined) && in_array($game->id, $gamesDeclined))
                                                    @include('components.badge', ['status' => 'Not Attending'])
                                                @else
                                                    @include('components.badge', ['status' => 'Not Yet Attending'])
                                                @endif
                                            </div>
                                        </div>

                                        <div class="text-xs text-slate-600 dark:text-slate-400 flex items-center gap-1.5 truncate">
                                            <i class="fa-solid fa-location-dot text-rose-500"></i>
                                            <span class="truncate">{{ $game->location }}</span>
                                        </div>
                                    </div>

                                    <div class="pt-3.5 mt-3.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                                        <span class="font-mono text-slate-600 dark:text-slate-400">
                                            <strong class="text-slate-900 dark:text-slate-100">{{ $game->players->count() }}</strong> Skaters • <strong class="text-slate-900 dark:text-slate-100">{{ $game->goalies->count() }}</strong> Goalies
                                        </span>
                                        <a href="/game/{{ $game->id }}" class="font-bold text-sky-600 dark:text-sky-400 hover:underline no-underline inline-flex items-center gap-1">
                                            <span>Details</span>
                                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @elseif(!$nextGame)
                        <div class="p-8 text-center rounded-3xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 text-slate-600 dark:text-slate-300 shadow-sm dark:shadow-md">
                            <i class="fa-regular fa-calendar-xmark text-3xl text-slate-400 dark:text-slate-500 mb-2"></i>
                            <p class="font-bold text-sm text-slate-800 dark:text-slate-100">No upcoming games scheduled right now</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Check back soon for new games added to the season.</p>
                        </div>
                    @endif
                </section>

                <!-- Previous Games & Scores Section -->
                <section class="space-y-4 pt-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-400 dark:bg-slate-500"></div>
                            <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-slate-100 tracking-wide">Previous Games &amp; Scores</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($currentSeason)
                                <span class="text-xs font-mono text-sky-700 dark:text-sky-300 font-bold bg-sky-50 dark:bg-sky-500/10 border border-sky-200 dark:border-sky-500/20 px-2 py-0.5 rounded">Season {{ $currentSeason->season_number }}</span>
                            @endif
                            <span class="text-xs font-mono text-slate-500 dark:text-slate-400 font-semibold">{{ $pastGames->count() }} Completed</span>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-3">
                        @foreach ($pastGames as $game)
                            <article class="rounded-2xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600 transition p-5 shadow-sm dark:shadow-md flex flex-col justify-between">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 truncate">{{ $game->title }}</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $game->game_time }} • {{ $game->location }}</p>
                                    </div>

                                    <!-- Scoreboard Score Pill -->
                                    <div class="shrink-0">
                                        @role('admin')
                                        <button type="button" class="inline-flex items-center rounded-2xl bg-slate-100 dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700/80 px-3 py-1.5 gap-3 score-pill hover:border-sky-500 dark:hover:border-sky-400 transition cursor-pointer shadow-inner" data-game-id="{{ $game->id }}" title="Click to edit game score">
                                        @else
                                        <div class="inline-flex items-center rounded-2xl bg-slate-100 dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700/80 px-3 py-1.5 gap-3 shadow-inner">
                                        @endrole
                                            <!-- Dark Team -->
                                            <div class="flex items-center gap-1.5 text-center">
                                                <span class="w-2 h-2 rounded-full bg-slate-800 dark:bg-slate-400 border border-slate-400"></span>
                                                <span class="text-[10px] uppercase font-mono tracking-wider text-slate-600 dark:text-slate-400 font-bold">Dark</span>
                                                <span class="text-sm sm:text-base font-black font-mono text-slate-900 dark:text-slate-100 ml-0.5" data-score-part="dark">{{ $game->dark_score }}</span>
                                            </div>

                                            <div class="w-px h-4 bg-slate-300 dark:bg-slate-700" aria-hidden="true"></div>

                                            <!-- Light Team -->
                                            <div class="flex items-center gap-1.5 text-center">
                                                <span class="w-2 h-2 rounded-full bg-sky-200 dark:bg-slate-200 border border-sky-400 dark:border-white"></span>
                                                <span class="text-[10px] uppercase font-mono tracking-wider text-slate-600 dark:text-slate-400 font-bold">Light</span>
                                                <span class="text-sm sm:text-base font-black font-mono text-sky-600 dark:text-cyan-300 ml-0.5" data-score-part="light">{{ $game->light_score }}</span>
                                            </div>
                                        @role('admin')
                                        </button>
                                        @else
                                        </div>
                                        @endrole
                                    </div>
                                </div>

                                <div class="pt-3.5 mt-3.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                                    <span class="font-mono text-slate-600 dark:text-slate-400">
                                        <strong class="text-slate-900 dark:text-slate-100">{{ $game->players->count() }}</strong> Skaters • <strong class="text-slate-900 dark:text-slate-100">{{ $game->goalies->count() }}</strong> Goalies
                                    </span>
                                    <a href="/game/{{ $game->id }}" class="font-bold text-sky-600 dark:text-sky-400 hover:underline no-underline inline-flex items-center gap-1">
                                        <span>Box Score</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if ($pastGames->isEmpty())
                        <div class="p-6 text-center rounded-2xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 text-slate-500 dark:text-slate-400 text-sm shadow-sm">
                            No past games recorded yet {{ $currentSeason ? 'for Season ' . $currentSeason->season_number : 'this season' }}.
                        </div>
                    @endif
                </section>
            </div>

            <!-- Secondary Station / Sidebar Panel (4 cols on desktop, responsive up to 3xl) -->
            <div class="col-span-12 lg:col-span-4 2xl:col-span-4 3xl:col-span-3 space-y-6">

                <!-- Player Status & Locker Card -->
                <div class="rounded-3xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 p-6 shadow-sm dark:shadow-md transition-colors">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-500 to-cyan-400 text-slate-950 font-black text-lg flex items-center justify-center shadow-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-black text-slate-900 dark:text-slate-100 truncate">{{ Auth::user()->name }}</h3>
                                @if(Auth::user()->jersey_number)
                                    <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-[#0f172a] text-slate-700 dark:text-slate-300">#{{ Auth::user()->jersey_number }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5 mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs">
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-[#0f172a] border border-slate-200/80 dark:border-slate-700/60">
                            <span class="text-[10px] uppercase font-mono font-semibold text-slate-500 dark:text-slate-400">Skill Level</span>
                            <div class="font-black text-sm text-sky-600 dark:text-sky-400 mt-0.5">Tier {{ Auth::user()->level ?? '3' }}/5</div>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-[#0f172a] border border-slate-200/80 dark:border-slate-700/60">
                            <span class="text-[10px] uppercase font-mono font-semibold text-slate-500 dark:text-slate-400">Position</span>
                            <div class="font-black text-sm text-slate-900 dark:text-slate-100 mt-0.5 capitalize">{{ Auth::user()->role_preference ?? 'Skater' }}</div>
                        </div>
                    </div>

                    <div class="mt-4 pt-2">
                        <a href="{{ route('profile') }}" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 text-xs font-bold transition no-underline shadow-sm">
                            <i class="fa-solid fa-user-pen text-sky-500"></i>
                            <span>Edit Locker Room Profile</span>
                        </a>
                    </div>
                </div>

                <!-- Season Pulse Card -->
                <div class="rounded-3xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 p-6 shadow-sm dark:shadow-md transition-colors space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-chart-simple text-sky-500"></i>
                            Season Snapshot
                        </span>
                        @if($currentSeason)
                            <span class="text-xs font-mono font-bold px-2 py-0.5 rounded-md bg-sky-100 dark:bg-sky-500/15 text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-500/30">
                                Season {{ $currentSeason->season_number }}
                            </span>
                        @endif
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-600 dark:text-slate-400">Upcoming Games</span>
                            <span class="font-mono font-black text-slate-900 dark:text-slate-100">{{ $upcomingGames->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-600 dark:text-slate-400">Completed Games</span>
                            <span class="font-mono font-black text-slate-900 dark:text-slate-100">{{ $pastGames->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs py-2">
                            <span class="text-slate-600 dark:text-slate-400">Team Balancing</span>
                            <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-[11px]"></i> Active
                            </span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('games.index') }}" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-slate-100 dark:bg-[#0f172a] hover:bg-slate-200 dark:hover:bg-slate-700/60 text-slate-800 dark:text-slate-200 text-xs font-bold transition no-underline">
                            <i class="fa-solid fa-calendar-days text-sky-500"></i>
                            <span>Full Season Schedule</span>
                        </a>
                    </div>
                </div>

                <!-- Goalie Crease Status Alert Widget -->
                @if($nextGame)
                    <div class="rounded-3xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 p-6 shadow-sm dark:shadow-md transition-colors space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-shield-halved text-cyan-500"></i>
                                Crease Radar
                            </span>
                            @if($nextGame->goalies->count() >= 2)
                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300">Creases Locked</span>
                            @else
                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-500/20 text-amber-900 dark:text-amber-300">Goalie Needed</span>
                            @endif
                        </div>

                        <div class="space-y-2 pt-1 text-xs">
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-[#0f172a] border border-slate-200/80 dark:border-slate-700/60">
                                <span class="text-slate-700 dark:text-slate-300 font-medium">Dark Team Net</span>
                                @if($nextGame->goalies->count() >= 1)
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1"><i class="fa-solid fa-check"></i> Confirmed</span>
                                @else
                                    <span class="font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> Open</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-[#0f172a] border border-slate-200/80 dark:border-slate-700/60">
                                <span class="text-slate-700 dark:text-slate-300 font-medium">Light Team Net</span>
                                @if($nextGame->goalies->count() >= 2)
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1"><i class="fa-solid fa-check"></i> Confirmed</span>
                                @else
                                    <span class="font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> Open</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // If URL contains ?startTour=1 then start intro.js tour
    (function(){
        const params = new URLSearchParams(window.location.search);
        if (params.get('startTour') === '1') {
            if (typeof introJs !== 'undefined') {
                try {
                    introJs().start();
                } catch(e) {
                    console.warn('intro.js start failed', e);
                }
            }
            // remove param so refreshing doesn't restart
            params.delete('startTour');
            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.history.replaceState({}, document.title, newUrl);
        }
    })();
</script>
<script>
    (() => {
        let csrf = null;

            function createModalHtml() {
            return `
            <div id="score-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-950/60 dark:bg-black/75 backdrop-blur-sm modal-backdrop z-40"></div>
                <div class="relative z-50 bg-white dark:bg-slate-900 border border-slate-200 dark:border-cyan-500/30 rounded-2xl p-6 w-full max-w-md shadow-2xl">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200 dark:border-slate-800">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-trophy text-sky-600 dark:text-cyan-400"></i>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Record Final Score</h3>
                        </div>
                        <button type="button" id="score-modal-close" class="text-slate-400 hover:text-slate-700 dark:hover:text-white transition" onclick="document.getElementById('score-cancel').click()">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                    <form id="score-form" class="space-y-4">
                        <input type="hidden" name="game_id" id="modal-game-id" />
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-50 dark:bg-slate-950/80 p-3 rounded-xl border border-slate-200 dark:border-slate-800">
                                <label class="text-xs uppercase font-mono font-bold text-slate-600 dark:text-slate-400 flex items-center gap-1.5 mb-1.5">
                                    <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                                    Dark Team
                                </label>
                                <input id="modal-dark-score" name="dark_score" type="number" min="0" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-xl font-bold font-mono text-slate-900 dark:text-white text-center focus:border-sky-500 dark:focus:border-cyan-400 focus:outline-none" />
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-950/80 p-3 rounded-xl border border-slate-200 dark:border-slate-800">
                                <label class="text-xs uppercase font-mono font-bold text-sky-700 dark:text-cyan-300 flex items-center gap-1.5 mb-1.5">
                                    <span class="w-2 h-2 rounded-full bg-sky-400 dark:bg-slate-200"></span>
                                    Light Team
                                </label>
                                <input id="modal-light-score" name="light_score" type="number" min="0" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-xl font-bold font-mono text-sky-700 dark:text-cyan-300 text-center focus:border-sky-500 dark:focus:border-cyan-400 focus:outline-none" />
                            </div>
                        </div>
                        <div class="flex justify-end gap-2.5 pt-2">
                            <button type="button" id="score-cancel" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold transition">Cancel</button>
                            <button type="submit" id="score-save" class="px-5 py-2 bg-sky-600 hover:bg-sky-500 dark:bg-gradient-to-r dark:from-cyan-500 dark:to-sky-600 dark:hover:from-cyan-400 text-white dark:text-slate-950 font-bold rounded-xl text-xs transition shadow-sm">Save Score</button>
                        </div>
                        <div id="score-error" class="text-rose-500 dark:text-rose-400 text-xs mt-2 hidden"></div>
                    </form>
                </div>
            </div>
            `;
        }

        document.addEventListener('DOMContentLoaded', () => {
            csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            // insert modal into body
            const wrapper = document.createElement('div');
            wrapper.innerHTML = createModalHtml();
            document.body.appendChild(wrapper.firstElementChild);

            const modal = document.getElementById('score-modal');
            const form = document.getElementById('score-form');
            const darkInput = document.getElementById('modal-dark-score');
            const lightInput = document.getElementById('modal-light-score');
            const gameIdInput = document.getElementById('modal-game-id');
            const errorEl = document.getElementById('score-error');

            function openModal(gameId, dark, light) {
                gameIdInput.value = gameId;
                darkInput.value = dark;
                lightInput.value = light;
                errorEl.classList.add('hidden');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            document.body.addEventListener('click', (e) => {
                const btn = e.target.closest('.score-pill');
                if (btn) {
                    e.preventDefault();
                    const gameId = btn.getAttribute('data-game-id');
                    // find current scores from the pill
                    const dark = btn.querySelector('[data-score-part="dark"]')?.textContent.trim() || '0';
                    const light = btn.querySelector('[data-score-part="light"]')?.textContent.trim() || '0';
                    openModal(gameId, dark, light);
                }
            });

            // close modal when clicking outside content (backdrop)
            modal.addEventListener('click', (e) => {
                if (e.target.classList.contains('modal-backdrop') || e.target.id === 'score-modal') {
                    closeModal();
                }
            });

            document.getElementById('score-cancel').addEventListener('click', (e) => {
                e.preventDefault();
                closeModal();
            });

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const gameId = gameIdInput.value;
                const dark = parseInt(darkInput.value || 0, 10);
                const light = parseInt(lightInput.value || 0, 10);
                errorEl.classList.add('hidden');

                try {
                    const res = await fetch(`/admin/game/${gameId}/score`, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrf || ''
                        },
                        body: JSON.stringify({ dark_score: dark, light_score: light })
                    });

                    let data = {};
                    try { data = await res.json(); } catch(e){ /* ignore parse errors */ }

                    if (!res.ok) {
                        if (res.status === 419) {
                            errorEl.textContent = 'Session expired or invalid CSRF token. Please refresh and try again.';
                        } else {
                            errorEl.textContent = data.error || (`Unable to save scores (status ${res.status})`);
                        }
                        errorEl.classList.remove('hidden');
                        return;
                    }

                    // update pill UI with new values
                    const pill = document.querySelector(`.score-pill[data-game-id="${gameId}"]`);
                    if (pill) {
                        const darkEl = pill.querySelector('[data-score-part="dark"]');
                        const lightEl = pill.querySelector('[data-score-part="light"]');
                        if (darkEl) darkEl.textContent = data.dark_score;
                        if (lightEl) lightEl.textContent = data.light_score;
                    }

                    closeModal();
                } catch (err) {
                    errorEl.textContent = 'Network error';
                    errorEl.classList.remove('hidden');
                }
            });
        });
    })();
</script>
@endpush
