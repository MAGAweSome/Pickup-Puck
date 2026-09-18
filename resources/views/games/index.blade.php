@extends('layouts.app')

@section('content')
    @php
        $isAdmin = auth()->check() && auth()->user()->hasRole('admin');
        $showPrice = auth()->check()
            && auth()->user()->role_preference !== \App\Enums\Games\GameRoles::Goalie->value;
        $isOnboarding = request()->boolean('onboarding');
    @endphp

    <div class="space-y-6 w-full">
        @if($isOnboarding)
            <div class="rounded-2xl border border-sky-300 dark:border-cyan-500/30 bg-sky-50 dark:bg-slate-900 p-4 shadow-sm flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🏒</span>
                    <div>
                        <h2 class="text-sm font-bold text-sky-800 dark:text-cyan-300 uppercase tracking-wider">Onboarding: Games Schedule</h2>
                        <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">Browse the game schedule, register attendance, or check Dark vs Light scores.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Schedule Header & Season Filter -->
        <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 p-6 shadow-sm dark:shadow-md transition-colors">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono font-bold bg-sky-50 dark:bg-sky-500/15 border border-sky-200 dark:border-sky-500/30 text-sky-700 dark:text-sky-300 mb-2">
                        <i class="fa-solid fa-calendar-days text-xs"></i>
                        <span>Season Fixtures &amp; Box Scores</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">Games Schedule</h1>
                </div>

                <div class="flex items-center gap-3">
                    @if(isset($seasons) && $seasons->count() > 0)
                        <form method="GET" action="{{ route('games.index') }}" class="m-0">
                            <label for="season_select" class="sr-only">Season</label>
                            <div class="relative">
                                <select id="season_select" name="season" onchange="this.form.submit()" class="bg-slate-50 dark:bg-[#0f172a] border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-xs sm:text-sm font-bold rounded-xl pl-3.5 pr-8 py-2 focus:border-sky-500 focus:outline-none cursor-pointer appearance-none shadow-sm">
                                    @foreach($seasons as $season)
                                        <option value="{{ $season->id }}" {{ (isset($currentSeason) && $currentSeason->id == $season->id) ? 'selected' : '' }}>Season {{ $season->season_number }}</option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            </div>
                        </form>
                    @elseif(isset($currentSeason))
                        <div class="text-xs sm:text-sm font-mono font-bold text-sky-700 dark:text-sky-300 bg-sky-50 dark:bg-[#0f172a] border border-sky-200 dark:border-slate-700 px-3.5 py-2 rounded-xl">Season {{ $currentSeason->season_number }}</div>
                    @endif

                    @if($isAdmin)
                        <a href="/admin/create_game" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 hover:bg-sky-500 dark:bg-sky-500 dark:hover:bg-sky-400 text-white dark:text-slate-950 font-black text-xs rounded-xl transition shadow-sm no-underline">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            <span class="hidden sm:inline">Add Game</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Cards Grid --}}
        <div class="grid gap-5 md:grid-cols-2 2xl:grid-cols-3">
            @if($isOnboarding && (!isset($games) || $games->isEmpty()))
                <article class="bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 rounded-2xl p-5 shadow-sm dark:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="text-lg text-slate-900 dark:text-slate-100 font-bold">Example Pickup Game</h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Fri, Jan 9 9:30 PM • 123 Example Arena</p>
                        </div>
                        @if($showPrice)
                            <div class="text-xs font-mono font-bold text-sky-600 dark:text-sky-400">$20.00</div>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                        <div class="text-slate-500 dark:text-slate-400 font-mono">Score: —</div>
                        <a id="onbGameDetailsLink" href="{{ route('onboarding.game-details', ['onboarding' => 1]) }}" class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-sky-500 text-slate-950 font-bold no-underline transition">Details</a>
                    </div>
                </article>
            @endif

            @forelse($games as $game)
                @php
                    $isPast = $game->time < \Carbon\Carbon::now();
                @endphp
                <article class="relative rounded-2xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 hover:border-sky-400 dark:hover:border-sky-400/50 transition duration-150 p-5 shadow-sm dark:shadow-md flex flex-col justify-between group">
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition truncate">{{ $game->title }}</h3>
                                <div class="flex items-center gap-3 mt-1.5 text-xs text-slate-500 dark:text-slate-400 flex-wrap">
                                    <span class="flex items-center gap-1.5 font-medium">
                                        <i class="fa-regular fa-calendar text-sky-500 text-[11px]"></i>
                                        <span>{{ $game->time->format('M d, Y g:i A') }}</span>
                                    </span>
                                    <span>•</span>
                                    <span class="flex items-center gap-1.5 truncate">
                                        <i class="fa-solid fa-location-dot text-rose-500 text-[11px]"></i>
                                        <span>{{ $game->location }}</span>
                                    </span>
                                </div>
                            </div>

                            @if($showPrice)
                                <div class="text-xs font-mono font-bold text-sky-600 dark:text-sky-400 shrink-0">${{ number_format($game->price, 2) }}</div>
                            @endif
                        </div>

                        <!-- Score / Status Pill -->
                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                            @if($isPast)
                                <div class="inline-flex items-center gap-3 px-3 py-1 bg-slate-100 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl font-mono text-xs">
                                    <span class="flex items-center gap-1 text-slate-600 dark:text-slate-400">
                                        <span class="w-2 h-2 rounded-full bg-slate-700 dark:bg-slate-500"></span>
                                        <span>Dark: <strong class="text-slate-900 dark:text-white">{{ $game->dark_score }}</strong></span>
                                    </span>
                                    <span class="text-slate-400">|</span>
                                    <span class="flex items-center gap-1 text-slate-600 dark:text-slate-400">
                                        <span class="w-2 h-2 rounded-full bg-sky-300 dark:bg-slate-200"></span>
                                        <span>Light: <strong class="text-sky-600 dark:text-cyan-300">{{ $game->light_score }}</strong></span>
                                    </span>
                                </div>
                            @else
                                <div class="inline-flex items-center gap-1.5 text-xs text-sky-600 dark:text-sky-400 font-mono font-bold">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Upcoming Game</span>
                                </div>
                            @endif

                            <div class="text-slate-500 dark:text-slate-400 font-mono text-[11px] font-semibold">
                                {{ $game->players->count() }}P • {{ $game->goalies->count() }}G
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('game_detail.game_id', ['game' => $game->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-sky-50 dark:bg-sky-500/15 hover:bg-sky-100 dark:hover:bg-sky-500/25 border border-sky-200 dark:border-sky-500/30 text-sky-700 dark:text-sky-300 hover:text-sky-900 dark:hover:text-white rounded-xl text-xs font-bold transition no-underline shadow-sm">
                                <span>Game Center</span>
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                            @include('components.add-to-calendar', ['game' => $game])
                        </div>

                        @if($isAdmin)
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('edit_game', ['game' => $game->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-amber-700 dark:text-amber-300 border border-slate-200 dark:border-slate-700 text-xs no-underline font-bold transition" title="Edit Game">
                                    <i class="fa-solid fa-pen text-[10px]"></i>
                                    <span>Edit</span>
                                </a>
                                <a href="{{ route('delete_game', ['game' => $game->id]) }}" onclick="return confirm('Are you sure you want to delete this game?');" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-rose-100 dark:hover:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-slate-200 dark:border-slate-700 text-xs no-underline font-bold transition" title="Delete Game">
                                    <i class="fa-solid fa-trash text-[10px]"></i>
                                    <span>Delete</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="col-span-full p-10 text-center rounded-3xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 text-slate-500 dark:text-slate-400 shadow-sm dark:shadow-md">
                    <i class="fa-regular fa-calendar-xmark text-3xl text-slate-400 dark:text-slate-500 mb-3 block"></i>
                    <p class="font-bold text-base text-slate-800 dark:text-slate-200">No games scheduled for this season yet.</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Check back later or select another season from the dropdown above.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
