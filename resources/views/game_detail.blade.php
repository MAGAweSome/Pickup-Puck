@extends('layouts.app')

@section('content')

<div class="w-full max-w-7xl 2xl:max-w-[1680px] 3xl:max-w-[1880px] mx-auto px-2 sm:px-4 py-6">
    <style>
        /* Control map sizing responsively to avoid it growing too tall on narrow screens */
        #mapWrap { min-height: 360px; }
        #mapWrap iframe { width:100%; height:auto; min-height:360px; display:block; }
        @media (min-width: 1024px) {
            /* desktop: keep taller map but bounded */
            #mapWrap { min-height: 640px; max-height: 900px; }
            /* make the iframe fill the mapWrap height on desktop so the dark footer area is the iframe background */
            #mapWrap iframe { height: 100%; }
        }
        @media (max-width: 1023px) {
            /* smaller screens: increase min-height to reduce gap under the Map location footer */
            /* ensure the iframe itself has a min-height so it fills the container */
            #mapWrap { min-height: 560px; max-height: 760px; }
            #mapWrap iframe { min-height: 560px; }
        }
        @media (max-width: 425px) {
            /* Stack header action buttons (Edit / Remove) on very small phones */
            .game-actions { flex-direction: column; align-items: stretch; gap: 0.5rem; }
            .game-actions a, .game-actions form { width: 100%; }
            .game-actions .inline-flex, .game-actions button { width: 100%; }
        }
    </style>
    @php
        // global total goalies used by forms and admin buttons
        $totalGoalies = (isset($goalies) ? count($goalies) : 0) + (isset($guestGoalies) ? count($guestGoalies) : 0);
        // whether the current user has marked 'cannot attend' for this game
        $meCannotAttend = false;
        if (auth()->check() && isset($cannotAttendingUsers)) {
            $meCannotAttend = $cannotAttendingUsers->contains('id', auth()->id());
        }
    @endphp
    @if(Session::has('success'))
        <div class="mb-4 p-3 rounded bg-emerald-600 text-white">{{ Session::get('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Full width details card (moved above the map) -->
        <div class="lg:col-span-3 relative z-30">
            <div class="relative z-30 rounded-3xl bg-white dark:bg-gradient-to-r dark:from-slate-950/95 dark:via-slate-900/90 dark:to-cyan-950/30 border border-slate-200 dark:border-cyan-500/25 p-6 sm:p-7 shadow-xl dark:shadow-[0_10px_35px_rgba(0,0,0,0.5)] backdrop-blur-md mb-2">
                <div class="absolute inset-0 rounded-3xl overflow-hidden pointer-events-none">
                    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-sky-500/10 dark:bg-cyan-500/10 rounded-full blur-3xl"></div>
                </div>

                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 relative z-30">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-sky-50 dark:bg-cyan-500/15 border border-sky-200 dark:border-cyan-500/30 text-sky-700 dark:text-cyan-300 mb-2">
                            <span class="w-2 h-2 rounded-full bg-sky-500 dark:bg-cyan-400 animate-pulse"></span>
                            <span>Pickup Puck Game Center</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">{{ $game->title }}</h1>
                        @if($game->description)
                            <p class="mt-1.5 text-xs sm:text-sm text-slate-600 dark:text-slate-300 max-w-2xl leading-relaxed">{{ $game->description }}</p>
                        @endif

                        <div class="mt-4 flex flex-wrap items-center gap-2.5 text-xs">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200">
                                <i class="fa-regular fa-calendar text-sky-500 dark:text-cyan-400"></i>
                                <span class="font-medium">{{ $game->game_time }}</span>
                            </div>

                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200">
                                <i class="fa-regular fa-hourglass-half text-sky-500 dark:text-cyan-400"></i>
                                <span class="font-medium">{{ $game->duration }} min session</span>
                            </div>

                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200">
                                <i class="fa-solid fa-location-dot text-rose-500"></i>
                                <a class="text-sky-600 dark:text-cyan-300 hover:text-sky-800 dark:hover:text-white transition no-underline font-medium" href="https://maps.google.com/?q={{ urlencode($game->location) }}" target="_blank">{{ $game->location }}</a>
                            </div>

                            @php
                                $showPrice = auth()->check()
                                    && auth()->user()->role_preference !== \App\Enums\Games\GameRoles::Goalie->value;
                            @endphp
                            @if($showPrice)
                                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-50 dark:bg-cyan-500/15 border border-sky-200 dark:border-cyan-500/30 text-sky-700 dark:text-cyan-300 font-mono font-bold">
                                    <span>${{ $game->price }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div id="gameHeaderActions" class="flex flex-wrap items-center gap-2.5 game-actions shrink-0 relative z-30">
                        @include('components.add-to-calendar', ['game' => $game])

                        @role('admin')
                            <a href="{{ route('edit_game', ['game' => $game->id]) }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 dark:from-cyan-500 dark:to-sky-600 dark:hover:from-cyan-400 dark:hover:to-sky-500 text-white dark:text-slate-950 font-bold rounded-xl text-xs shadow-md transition no-underline">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                <span>Edit Game</span>
                            </a>
                        @endrole

                        {{-- Non-admin attendees can remove themselves from the game. If the user marked cannot-attend, show non-clickable status. --}}
                        @if(auth()->check())
                            @if(!empty($user_registered) && $user_registered)
                                <form method="POST" action="{{ route('game_remove_self', ['game' => $game->id]) }}" data-async-game-form>
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/20 dark:hover:bg-rose-500/30 border border-rose-300 dark:border-rose-500/40 text-rose-700 dark:text-rose-300 font-bold rounded-xl text-xs transition">
                                        <i class="fa-solid fa-user-xmark text-xs"></i>
                                        <span>Remove Myself</span>
                                    </button>
                                </form>
                            @elseif(!empty($meCannotAttend) && $meCannotAttend)
                                <span class="inline-flex items-center gap-2 px-3.5 py-2 bg-rose-50 dark:bg-rose-500/20 border border-rose-300 dark:border-rose-500/40 text-rose-700 dark:text-rose-300 font-bold rounded-xl text-xs">
                                    <i class="fa-solid fa-ban text-xs"></i>
                                    <span>Not Attending</span>
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Main column: Map + details -->
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-xl">
                <div id="mapWrap" class="w-full flex flex-col">
                    <iframe id="gameMap" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q={{ urlencode($game->location) }}&amp;z=14&amp;output=embed" class="w-full" frameborder="0" marginheight="0" marginwidth="0" loading="lazy"></iframe>

                    <div class="p-3.5 bg-slate-50 dark:bg-slate-950/90 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div class="text-xs text-slate-600 dark:text-slate-400 flex items-center gap-2">
                            <i class="fa-solid fa-map-pin text-rose-500"></i>
                            <span>Arena Location</span>
                        </div>
                        <div>
                            <button id="retryMapBtn" type="button" class="px-3 py-1 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-lg text-xs hidden">Retry Map</button>
                            <a href="https://maps.google.com/?q={{ urlencode($game->location) }}" target="_blank" class="text-sky-600 dark:text-cyan-300 hover:text-sky-800 dark:hover:text-white text-xs font-semibold no-underline transition flex items-center gap-1">
                                <span>Open Google Maps</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accept / RSVP forms -->
            <div id="acceptGameWrap" class="grid grid-cols-1 gap-4">
                @if($user_registered == false)
                    <div class="rounded-2xl bg-white dark:bg-gradient-to-b dark:from-slate-900/90 dark:to-slate-950/90 border border-sky-200 dark:border-cyan-500/30 p-5 shadow-xl backdrop-blur-md">
                        <div class="flex items-center gap-2.5 mb-3">
                            <span class="w-2 h-2 rounded-full bg-sky-500 dark:bg-cyan-400 animate-pulse"></span>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-wide">RSVP for This Game</h3>
                        </div>
                        <div class="flex flex-wrap gap-2.5 items-center accept-controls">
                            <form action="{{ route('game_detail_update.game_id', ['game' => $game->id]) }}" method="POST" class="flex-1 min-w-0" data-async-game-form>
                                @csrf
                                <div class="flex gap-2">
                                    <select required name="gameRole" id="gameRole" class="flex-1 min-w-0 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-slate-100 text-sm focus:border-sky-500 dark:focus:border-cyan-400 focus:outline-none cursor-pointer">
                                        <option value="" selected disabled hidden>Select your position...</option>
                                            @foreach ($GAME_ROLES as $gamerole)
                                                @php $isGoalieRole = ($gamerole == App\Enums\Games\GameRoles::Goalie); @endphp
                                                <option value="{{ $gamerole }}" {{ $gamerole == App\Enums\Games\GameRoles::tryFrom(Auth::user()->role_preference) ? 'selected' : '' }} @if($isGoalieRole && $totalGoalies >= 2) disabled title="Goalie roster is full" @endif>{{ $gamerole->name }}</option>
                                            @endforeach
                                    </select>
                                    <button class="px-5 py-2.5 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 dark:from-cyan-500 dark:to-sky-600 dark:hover:from-cyan-400 dark:hover:to-sky-500 text-white dark:text-slate-950 font-bold rounded-xl text-sm whitespace-nowrap shadow-md dark:shadow-[0_0_15px_rgba(56,189,248,0.3)] transition" type="submit" id="accept_game_submit_button" name="game">Accept</button>
                                </div>
                                @error('gameRole') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5">{{ $message }}</div> @enderror
                            </form>

                            <form action="{{ route('game_detail_cannot_attend', ['game' => $game->id]) }}" method="POST" class="shrink-0" data-async-game-form>
                                @csrf
                                <button type="submit" class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/20 dark:hover:bg-rose-500/30 border border-rose-300 dark:border-rose-500/40 text-rose-700 dark:text-rose-300 font-bold rounded-xl text-sm whitespace-nowrap transition">Cannot Attend</button>
                            </form>
                    <style>
                        @media (max-width: 560px) {
                            .accept-controls { flex-direction: column; align-items: stretch; }
                            .accept-controls > form { width: 100%; }
                            .accept-controls > form .flex { flex-direction: column; gap: 0.5rem; }
                            .accept-controls select { width: 100%; }
                            .accept-controls button { width: 100%; }
                            .accept-controls .shrink-0 { width: 100%; }
                        }
                    </style>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar: quick stats -->
        <aside id="sidebarCol" class="space-y-4 lg:self-stretch lg:flex lg:flex-col lg:gap-4 lg:col-span-1">
            <div id="quickInfoCard" class="relative overflow-hidden rounded-2xl bg-white dark:bg-gradient-to-b dark:from-slate-900/90 dark:to-slate-950/90 border border-slate-200 dark:border-cyan-500/25 p-5 shadow-xl backdrop-blur-md ring-1 ring-slate-950/5 dark:ring-white/5">
                <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-chart-simple text-sky-500 dark:text-cyan-400"></i>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white tracking-wide">Game Quick Info</h4>
                    </div>
                    <span class="text-[10px] font-mono uppercase tracking-wider text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/80 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700/60">This Game</span>
                </div>

                <div class="mt-3 text-xs space-y-2.5">
                    <div class="flex items-center justify-between py-1">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Registered Skaters</span>
                        <span class="font-black font-mono text-sky-600 dark:text-cyan-300 text-sm">{{ count($players) + count($guestPlayers) }}</span>
                    </div>
                    <div class="h-px bg-slate-100 dark:bg-slate-800"></div>
                    <div class="flex items-center justify-between py-1">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Registered Goalies</span>
                        <span class="font-black font-mono text-indigo-600 dark:text-sky-400 text-sm">{{ count($goalies) + count($guestGoalies) }} / 2</span>
                    </div>
                    @if($showPrice)
                        <div class="h-px bg-slate-100 dark:bg-slate-800"></div>
                        <div class="flex items-center justify-between py-1">
                            <span class="text-slate-600 dark:text-slate-400 font-medium">Game Fee</span>
                            <span class="font-black font-mono text-sky-600 dark:text-cyan-300 text-sm">${{ $game->price }}</span>
                        </div>
                    @endif
                    <div class="h-px bg-slate-100 dark:bg-slate-800"></div>
                    <div class="flex items-center justify-between py-1">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Season</span>
                        <span class="font-mono text-slate-900 dark:text-slate-200 font-semibold">{{ isset($currentSeason) && $currentSeason ? 'Season ' . $currentSeason->season_number : '—' }}</span>
                    </div>
                </div>
            </div>

            <div id="bringGuestCard" class="rounded-2xl bg-white dark:bg-gradient-to-b dark:from-slate-900/90 dark:to-slate-950/90 border border-slate-200 dark:border-slate-800 p-5 shadow-xl backdrop-blur-md">
                <div class="flex items-center gap-2 pb-2.5 mb-2 border-b border-slate-200 dark:border-slate-800">
                    <i class="fa-solid fa-user-plus text-sky-500 dark:text-cyan-400 text-xs"></i>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider font-mono">Bring a Guest</h4>
                </div>
                <form id="bringGuestForm" action="{{ route('game_detail_update_guest.game_id', ['game' => $game->id]) }}" method="POST" class="mt-2 flex flex-col space-y-2.5" data-async-game-form>
                    @csrf
                    <div class="relative">
                        <input type="text" id="guestName" name="guestName" autocomplete="off" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:border-sky-500 dark:focus:border-cyan-400 focus:outline-none" placeholder="Guest full name" minlength="4" required>
                        <div id="guestList" class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-cyan-500/30 rounded-xl shadow-2xl max-h-48 overflow-auto py-1 text-xs text-slate-900 dark:text-slate-100"></div>
                    </div>
                    <select required name="gameRole" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:border-sky-500 dark:focus:border-cyan-400 focus:outline-none cursor-pointer">
                        <option value="" selected disabled hidden>Position</option>
                        @foreach ($GAME_ROLES as $gamerole)
                            @php $isGoalieRole = ($gamerole == App\Enums\Games\GameRoles::Goalie); @endphp
                            <option value="{{ $gamerole }}" {{ $gamerole == App\Enums\Games\GameRoles::tryFrom(Auth::user()->role_preference) ? 'selected' : '' }} @if($isGoalieRole && $totalGoalies >= 2) disabled title="Goalie roster is full" @endif>{{ $gamerole->name }}</option>
                        @endforeach
                    </select>
                    <div>
                        <label for="level" class="block text-[11px] font-mono text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1">Guest Skill Level</label>
                        @php
                            $levelDescriptions = [
                                1 => 'Beginner / low rec',
                                2 => 'Recreational',
                                3 => 'Intermediate / competitive',
                                4 => 'Advanced / high skill',
                            ];
                        @endphp
                        <select name="level" id="level" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:border-sky-500 dark:focus:border-cyan-400 focus:outline-none cursor-pointer">
                            @for ($i = 1; $i <= 4; $i++)
                                <option value="{{ $i }}" {{ (int) old('level', 2) === $i ? 'selected' : '' }}>Level {{ $i }} — {{ $levelDescriptions[$i] }}</option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit" class="w-full mt-1 px-4 py-2 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 dark:from-cyan-500 dark:to-sky-600 dark:hover:from-cyan-400 dark:hover:to-sky-500 text-white dark:text-slate-950 font-bold rounded-xl text-xs transition shadow-md">Add Guest</button>
                    @error('guestName') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</div> @enderror
                </form>
            </div>
        </aside>

        @role('admin')
            <!-- Not Yet Attending (admin only) -->
            <div id="notAttendingWrap" class="lg:col-span-3">
                @if(isset($notAttendingUsers) && $notAttendingUsers->isNotEmpty())
                    <div class="rounded-2xl bg-white dark:bg-gradient-to-b dark:from-slate-900/90 dark:to-slate-950/90 border border-slate-200 dark:border-slate-800 p-5 shadow-xl backdrop-blur-md">
                        <div class="flex items-center justify-between gap-3 pb-3 mb-3 border-b border-slate-200 dark:border-slate-800">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-user-clock text-amber-500 dark:text-amber-400 text-sm"></i>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-mono">Not Yet Attending</h3>
                            </div>
                            <span class="text-xs font-mono font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 px-2.5 py-0.5 rounded-full">{{ $notAttendingUsers->count() }} Players</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">Add registered players directly into this game (admin only).</p>

                        <div class="max-h-72 overflow-auto rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/80 p-1">
                            <ul class="divide-y divide-slate-200 dark:divide-slate-800/80">
                                @foreach($notAttendingUsers as $u)
                                    <li class="p-2.5 flex items-center justify-between gap-3 hover:bg-slate-100 dark:hover:bg-slate-900/60 rounded-lg transition">
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $u->name }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $u->email }}</div>
                                        </div>

                                        <form class="flex items-center gap-2" method="POST" action="{{ route('admin_game_detail_update.game_id.user_id', ['game' => $game->id, 'user_id' => $u->id]) }}" data-async-game-form>
                                            @csrf
                                            <select name="gameRole" class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-800 dark:text-slate-200 text-xs focus:border-sky-500 dark:focus:border-cyan-400 focus:outline-none cursor-pointer">
                                                @foreach ($GAME_ROLES as $gamerole)
                                                    @php $isGoalieRole = ($gamerole == App\Enums\Games\GameRoles::Goalie); @endphp
                                                    <option value="{{ $gamerole }}" @if($gamerole == App\Enums\Games\GameRoles::Player) selected @endif @if($isGoalieRole && $totalGoalies >= 2) disabled title="Goalie roster is full" @endif>
                                                        {{ $gamerole->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="px-3 py-1.5 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 dark:from-cyan-500 dark:to-sky-600 dark:hover:from-cyan-400 dark:hover:to-sky-500 text-white dark:text-slate-950 font-bold rounded-lg text-xs transition shadow-sm">Add</button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @error('gameRole') <div class="text-rose-500 dark:text-rose-400 text-xs mt-2">{{ $message }}</div> @enderror
                    </div>
                @endif
            </div>
        @endrole

        @role('admin')
            <div id="cannotAttendingWrap" class="lg:col-span-3">
                @if(isset($cannotAttendingUsers) && $cannotAttendingUsers->isNotEmpty())
                    <div class="rounded-2xl bg-white dark:bg-gradient-to-b dark:from-slate-900/90 dark:to-slate-950/90 border border-slate-200 dark:border-slate-800 p-5 shadow-xl backdrop-blur-md mt-2">
                        <div class="flex items-center justify-between gap-3 pb-3 mb-3 border-b border-slate-200 dark:border-slate-800">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-user-xmark text-rose-500 dark:text-rose-400 text-sm"></i>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-mono">Cannot Attend</h3>
                            </div>
                            <span class="text-xs font-mono font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 px-2.5 py-0.5 rounded-full">{{ $cannotAttendingUsers->count() }} Players</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">Players who confirmed they are unavailable for this session.</p>

                        <div class="max-h-56 overflow-auto rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/80 p-1">
                            <ul class="divide-y divide-slate-200 dark:divide-slate-800/80">
                                @foreach($cannotAttendingUsers as $u)
                                    <li class="p-2.5 flex items-center justify-between gap-3 hover:bg-slate-100 dark:hover:bg-slate-900/60 rounded-lg transition">
                                        <div class="min-w-0">
                                            <div class="text-sm font-medium text-slate-900 dark:text-slate-300 truncate">{{ $u->name }}</div>
                                            <div class="text-xs text-slate-500 truncate">{{ $u->email }}</div>
                                        </div>
                                        <span class="text-[10px] font-mono text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/15 border border-rose-200 dark:border-rose-500/30 px-2 py-0.5 rounded">Declined</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        @endrole

        @role('admin')
            <div class="lg:col-span-3">
                <div class="rounded-2xl bg-white dark:bg-gradient-to-b dark:from-slate-900/90 dark:to-slate-950/90 border border-slate-200 dark:border-cyan-500/20 p-5 shadow-xl backdrop-blur-md mt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-sky-500 dark:text-cyan-400"></i>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-mono">Broadcast Game Reminder</h3>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">Open email client with all attending players to send pre-game updates.</p>
                    </div>

                    <div class="shrink-0">
                        @if($emailMailtoLink)
                            <a href="{{ $emailMailtoLink }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 dark:from-cyan-500 dark:to-sky-600 dark:hover:from-cyan-400 dark:hover:to-sky-500 text-white dark:text-slate-950 font-bold rounded-xl text-xs transition shadow-md no-underline">
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                                <span>Email All Players</span>
                            </a>
                        @else
                            <button disabled class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold cursor-not-allowed opacity-60">
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                                <span>Email All Players</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endrole

        <!-- Full width row: Roster + Teams (span to sidebar edge) -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Roster -->
            <div id="attendanceLists" class="rounded-3xl bg-white dark:bg-gradient-to-b dark:from-slate-900/90 dark:to-slate-950/90 border border-slate-200 dark:border-slate-800 p-6 shadow-xl backdrop-blur-md">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-clipboard-user text-sky-600 dark:text-cyan-400 text-lg"></i>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-wide">Attending Roster</h3>
                    </div>
                    <span class="text-xs font-mono text-slate-600 dark:text-slate-400">{{ (count($players) + count($guestPlayers) + count($goalies) + count($guestGoalies)) }} Attending</span>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Goalies Column -->
                    <div>
                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-200 dark:border-slate-800/80">
                            <span class="text-xs uppercase font-mono font-bold tracking-wider text-indigo-600 dark:text-sky-400 flex items-center gap-2">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>Goalie Crease</span>
                            </span>
                            <span class="text-xs font-mono text-slate-600 dark:text-slate-400">{{ $totalGoalies }} / 2</span>
                        </div>

                        <ul class="space-y-2">
                            @foreach($goalies as $goalie_id => $goalie_name)
                                <li class="bg-slate-50 dark:bg-slate-950/90 border border-slate-200 dark:border-cyan-500/30 rounded-xl px-3 py-2 flex items-center justify-between gap-2 shadow-sm" data-user-id="{{ $goalie_id }}">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-sky-100 dark:bg-cyan-500/20 text-sky-700 dark:text-cyan-300 border border-sky-300 dark:border-cyan-500/40 shrink-0">G</span>
                                        <span class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $goalie_name }}</span>
                                    </div>
                                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                                        <div class="relative">
                                            <button class="player-options-btn px-2 py-1 rounded text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-800 transition text-xs">⋮</button>
                                            <div class="player-options-menu hidden absolute right-0 mt-2 w-44 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl z-50 py-1 text-xs">
                                                <button data-user-id="{{ $goalie_id }}" data-role="player" class="w-full text-left px-3 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white change-player-role">Make Skater</button>
                                                <button data-user-id="{{ $goalie_id }}" class="w-full text-left px-3 py-2 text-rose-600 dark:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-800 remove-player">Remove</button>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endforeach

                            @foreach($guestGoalies as $guest)
                                <li class="bg-slate-50 dark:bg-slate-950/90 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 flex items-center justify-between gap-2 text-slate-800 dark:text-slate-300" data-guest-id="{{ $guest->id ?? '' }}">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-sky-100 dark:bg-cyan-500/15 text-sky-700 dark:text-cyan-400 border border-sky-300 dark:border-cyan-500/30 shrink-0">G</span>
                                        <span class="text-sm truncate">{{ $guest->name ?? $guest }}</span>
                                        <span class="text-[10px] font-mono text-slate-600 dark:text-slate-400 bg-slate-200/70 dark:bg-slate-900 px-1.5 py-0.5 rounded border border-slate-300 dark:border-slate-800">Guest</span>
                                    </div>
                                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                                        <div class="relative">
                                            <button class="player-options-btn px-2 py-1 rounded text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-800 transition text-xs">⋮</button>
                                            <div class="player-options-menu hidden absolute right-0 mt-2 w-44 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl z-50 py-1 text-xs">
                                                <button data-guest-id="{{ $guest->id ?? '' }}" data-role="player" class="w-full text-left px-3 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white make-guest-role">Make Skater</button>
                                                <button data-guest-id="{{ $guest->id ?? '' }}" class="w-full text-left px-3 py-2 text-rose-600 dark:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-800 admin-remove-guest">Remove</button>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endforeach

                            @for ($i = $totalGoalies; $i < 2; $i++)
                                <li class="bg-slate-100/60 dark:bg-slate-950/40 border border-dashed border-slate-300 dark:border-slate-800 rounded-xl px-3 py-2 text-xs font-mono text-slate-500 flex items-center gap-2">
                                    <i class="fa-regular fa-circle-dot text-slate-400 dark:text-slate-600"></i>
                                    <span>Empty Net Spot</span>
                                </li>
                            @endfor
                        </ul>
                    </div>

                    <!-- Skaters Column -->
                    <div>
                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-200 dark:border-slate-800/80">
                            <span class="text-xs uppercase font-mono font-bold tracking-wider text-sky-600 dark:text-cyan-400 flex items-center gap-2">
                                <i class="fa-solid fa-person-skating"></i>
                                <span>Skaters</span>
                            </span>
                            <span class="text-xs font-mono text-slate-600 dark:text-slate-400">{{ count($players) + count($guestPlayers) }}</span>
                        </div>

                        <ul class="space-y-2">
                            @foreach($players as $player_id => $player_name)
                                <li class="bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 rounded-xl px-3 py-2 flex items-center justify-between gap-2 transition" data-user-id="{{ $player_id }}">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $player_name }}</span>
                                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                                        <div class="relative">
                                            <button class="player-options-btn px-2 py-1 rounded text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-800 transition text-xs">⋮</button>
                                            <div class="player-options-menu hidden absolute right-0 mt-2 w-44 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl z-50 py-1 text-xs">
                                                @php $canMakeGoalie = ($totalGoalies < 2); @endphp
                                                <button data-user-id="{{ $player_id }}" data-role="goalie" class="w-full text-left px-3 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white change-player-role @if(!$canMakeGoalie) opacity-50 pointer-events-none @endif" @if(!$canMakeGoalie) title="Goalie roster is full" disabled @endif>Make Goalie</button>
                                                <button data-user-id="{{ $player_id }}" class="w-full text-left px-3 py-2 text-rose-600 dark:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-800 remove-player">Remove</button>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endforeach

                            @foreach($guestPlayers as $guest)
                                <li class="bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 flex items-center justify-between gap-2 text-slate-800 dark:text-slate-300" data-guest-id="{{ $guest->id ?? '' }}">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="text-sm truncate">{{ $guest->name ?? $guest }}</span>
                                        <span class="text-[10px] font-mono text-slate-600 dark:text-slate-400 bg-slate-200/70 dark:bg-slate-900 px-1.5 py-0.5 rounded border border-slate-300 dark:border-slate-800">Guest</span>
                                    </div>
                                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                                        <div class="relative">
                                            <button class="player-options-btn px-2 py-1 rounded text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-800 transition text-xs">⋮</button>
                                            <div class="player-options-menu hidden absolute right-0 mt-2 w-44 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl z-50 py-1 text-xs">
                                                @php $canMakeGoalieGuest = ($totalGoalies < 2); @endphp
                                                <button data-guest-id="{{ $guest->id ?? '' }}" data-role="goalie" class="w-full text-left px-3 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white make-guest-role @if(!$canMakeGoalieGuest) opacity-50 pointer-events-none @endif" @if(!$canMakeGoalieGuest) title="Goalie roster is full" disabled @endif>Make Goalie</button>
                                                <button data-guest-id="{{ $guest->id ?? '' }}" class="w-full text-left px-3 py-2 text-rose-600 dark:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-800 admin-remove-guest">Remove</button>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Teams (Live Auto-Reveal at T-30) -->
            <div
                id="gameTeam"
                class="rounded-3xl bg-white dark:bg-gradient-to-b dark:from-slate-900/90 dark:via-slate-950/90 dark:to-slate-950 border border-slate-200 dark:border-cyan-500/30 p-6 sm:p-7 shadow-xl dark:shadow-2xl relative overflow-hidden backdrop-blur-md transition-all duration-300"
                x-data="liveTeamsReveal({
                    gameId: {{ $game->id }},
                    revealTimestamp: {{ $teamsRevealAt->timestamp }},
                    isReady: {{ (!empty($teamsReady) && $teamsReady) ? 'true' : 'false' }}
                })"
                x-init="initTimer()"
            >
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-people-group text-sky-600 dark:text-cyan-400 text-xl"></i>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Balanced Teams</h3>
                            <span class="text-xs text-slate-600 dark:text-slate-400">Rotated weekly using snake coin-flip balancing</span>
                        </div>
                    </div>

                    <!-- Live Indicator / Status Badge -->
                    <div>
                        <template x-if="ready">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-300 dark:border-emerald-400/40 text-emerald-700 dark:text-emerald-300 shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                                <span>Rosters Revealed</span>
                            </span>
                        </template>

                        <template x-if="!ready">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-sky-50 dark:bg-cyan-500/15 border border-sky-200 dark:border-cyan-500/30 text-sky-700 dark:text-cyan-300 shadow-sm animate-pulse">
                                <i class="fa-solid fa-clock text-[10px]"></i>
                                <span>Live Reveal at T-30</span>
                            </span>
                        </template>
                    </div>
                </div>

                <!-- Unrevealed State: Live Countdown Flip Card -->
                <div
                    x-show="!ready"
                    x-transition:leave="transition ease-in duration-300 transform"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="py-6 px-4 text-center"
                >
                    <div class="max-w-md mx-auto space-y-4">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-cyan-500/30 text-sky-600 dark:text-cyan-300 shadow-md mb-1">
                            <i class="fa-solid fa-stopwatch text-2xl" :class="{ 'animate-bounce': isRevealing }"></i>
                        </div>

                        <div>
                            <h4 class="text-lg font-bold text-slate-900 dark:text-white tracking-wide">Roster Lock & Balancing Countdown</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                                Teams balance automatically and reveal 30 minutes before puck drop.
                            </p>
                        </div>

                        <!-- Digital Countdown Clock -->
                        <div class="grid grid-cols-3 gap-3 max-w-xs mx-auto py-2">
                            <!-- Hours -->
                            <div class="bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-3 px-2 shadow-inner">
                                <div class="text-3xl font-mono font-black text-slate-900 dark:text-white" x-text="hours">00</div>
                                <div class="text-[9px] tracking-widest uppercase font-mono text-slate-500 dark:text-slate-500 mt-0.5">Hours</div>
                            </div>
                            <!-- Minutes -->
                            <div class="bg-sky-50 dark:bg-slate-950 border border-sky-200 dark:border-cyan-500/30 rounded-2xl py-3 px-2 shadow-inner">
                                <div class="text-3xl font-mono font-black text-sky-600 dark:text-cyan-300" x-text="minutes">00</div>
                                <div class="text-[9px] tracking-widest uppercase font-mono text-sky-600 dark:text-cyan-400 mt-0.5">Mins</div>
                            </div>
                            <!-- Seconds -->
                            <div class="bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-3 px-2 shadow-inner">
                                <div class="text-3xl font-mono font-black text-slate-900 dark:text-white" x-text="seconds">00</div>
                                <div class="text-[9px] tracking-widest uppercase font-mono text-slate-500 dark:text-slate-500 mt-0.5">Secs</div>
                            </div>
                        </div>

                        <!-- Live Status Message -->
                        <div class="text-xs text-slate-600 dark:text-slate-400 flex items-center justify-center gap-2 pt-1 font-mono">
                            <template x-if="isRevealing">
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold inline-flex items-center gap-1.5 animate-pulse">
                                    <svg class="animate-spin h-3.5 w-3.5 text-emerald-600 dark:text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    <span>T-30 Reached! Unlocking rosters live...</span>
                                </span>
                            </template>
                            <template x-if="!isRevealing">
                                <span>
                                    Reveals at <strong class="text-slate-900 dark:text-slate-200">{{ $teamsRevealAt->format('g:i A') }}</strong> • Page auto-updates
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Revealed State: Dark & Light Rosters -->
                <div
                    x-show="ready"
                    x-cloak
                    x-transition:enter="transition ease-out duration-500 transform"
                    x-transition:enter-start="opacity-0 translate-y-3 scale-98"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    id="teamsRosterContainer"
                >
                    @if(!empty($teamsReady) && $teamsReady)
                        @include('components.teams_roster')
                    @endif
                </div>
            </div>
        </div>

        

@push('scripts')
<script>
    // Asynchronously refresh dynamic game sections without page reload
    let isRefreshingSections = false;
    async function refreshGameSections(toastMessage = null, toastType = 'success') {
        if (isRefreshingSections) return;
        isRefreshingSections = true;
        try {
            const response = await fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                }
            });
            if (!response.ok) throw new Error('Network error fetching game update');
            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');

            const sectionIds = [
                'gameHeaderActions',
                'acceptGameWrap',
                'quickInfoCard',
                'attendanceLists',
                'notAttendingWrap',
                'cannotAttendingWrap'
            ];

            sectionIds.forEach(id => {
                const currentEl = document.getElementById(id);
                const newEl = doc.getElementById(id);
                if (currentEl && newEl) {
                    currentEl.innerHTML = newEl.innerHTML;
                } else if (currentEl && !newEl) {
                    currentEl.innerHTML = '';
                }
            });

            // Update Bring a Guest card only if the user is not actively typing
            const guestInput = document.getElementById('guestName');
            const isTyping = guestInput && (document.activeElement === guestInput || guestInput.value.trim().length > 0);
            if (!isTyping) {
                const curBring = document.getElementById('bringGuestCard');
                const newBring = doc.getElementById('bringGuestCard');
                if (curBring && newBring) {
                    curBring.innerHTML = newBring.innerHTML;
                }
            }

            // Update teams roster if revealed
            const curTeams = document.getElementById('teamsRosterContainer');
            const newTeams = doc.getElementById('teamsRosterContainer');
            if (curTeams && newTeams && newTeams.innerHTML.trim().length > 0) {
                curTeams.innerHTML = newTeams.innerHTML;
            }

            if (typeof window.matchMapHeight === 'function') {
                window.matchMapHeight();
            }

            if (toastMessage && window.showToast) {
                window.showToast(toastMessage, toastType);
            }
        } catch (err) {
            console.error('refreshGameSections error:', err);
            if (toastMessage && window.showToast) {
                window.showToast(toastMessage, toastType);
            }
        } finally {
            isRefreshingSections = false;
        }
    }

    // Intercept form submissions for Accept Game, Cannot Attend, Bring a Guest, Admin Add Player, and Remove Myself
    document.addEventListener('submit', async function (e) {
        const form = e.target.closest('form[data-async-game-form]');
        if (!form) return;

        e.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Saving...';
        }

        try {
            const formData = new FormData(form);
            const actionUrl = form.getAttribute('action') || window.location.href;
            const method = (form.getAttribute('method') || 'POST').toUpperCase();

            const response = await fetch(actionUrl, {
                method: method,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            let data = null;
            try {
                data = await response.json();
            } catch (jsonErr) {
                // not a JSON response
            }

            if (response.ok && (!data || data.success !== false)) {
                const msg = (data && data.message) ? data.message : 'Updated successfully!';
                if (form.id === 'bringGuestForm' || form.closest('#bringGuestCard')) {
                    form.reset();
                    const guestListEl = document.getElementById('guestList');
                    if (guestListEl) guestListEl.classList.add('hidden');
                }
                await refreshGameSections(msg, 'success');
            } else {
                let errorMsg = 'Unable to complete action';
                if (data && data.error) {
                    errorMsg = data.error;
                } else if (data && data.errors) {
                    errorMsg = Object.values(data.errors).flat().join(' ');
                } else if (data && data.message) {
                    errorMsg = data.message;
                }
                if (window.showToast) {
                    window.showToast(errorMsg, 'error');
                } else {
                    alert(errorMsg);
                }
            }
        } catch (err) {
            console.error('Async form error:', err);
            if (window.showToast) {
                window.showToast('Network error submitting request', 'error');
            } else {
                alert('Network error submitting request');
            }
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        }
    });

    // Guest search and autocomplete (delegated so it stays active across DOM refreshes)
    $(document).ready(function(){
        $(document).on('input keyup', '#guestName', function(){
            var value = ($(this).val() || '').trim();
            if (value.length === 0) {
                $('#guestList').addClass('hidden').html('');
                return;
            }
            $.ajax({ 
                url: "{{$game->id}}/search", 
                type: "GET", 
                data: {'guestName': value}, 
                success: function(data){
                    if (data && data.trim().length > 0) {
                        $('#guestList').removeClass('hidden').html(data);
                    } else {
                        $('#guestList').addClass('hidden').html('');
                    }
                } 
            });
        });

        // Only handle clicks on search result items inside #guestList
        $(document).on('click', '#guestList li', function(e){
            e.stopPropagation();
            var value = $(this).text().trim();
            var level = $(this).data('level');
            $('#guestName').val(value);
            if (typeof level !== 'undefined' && level !== null) {
                $('#level').val(level);
            }
            $('#guestList').addClass('hidden').html('');
        });

        // Clicking outside the input/suggestion dropdown closes it
        $(document).on('click', function(e){
            const target = e.target;
            if (!target) return;
            const isInDropdown = $(target).closest('#guestList').length > 0;
            const isInInput = $(target).closest('#guestName').length > 0;
            if (!isInDropdown && !isInInput) {
                $('#guestList').addClass('hidden');
            }
        });
    });

    // Delegated player options menu toggle and admin operations
    document.addEventListener('click', function (e) {
        function findGuestIdFrom(el) {
            if (!el) return '';
            try {
                if (el.getAttribute) {
                    const direct = el.getAttribute('data-guest-id');
                    if (direct) return direct;
                }
                const li = el.closest ? el.closest('li[data-guest-id]') : null;
                if (li) {
                    const v = li.getAttribute('data-guest-id');
                    if (v) return v;
                }
                const rel = el.closest ? el.closest('.relative') : null;
                if (rel) {
                    const outerLi = rel.closest ? rel.closest('li[data-guest-id]') : null;
                    if (outerLi) return outerLi.getAttribute('data-guest-id') || '';
                }
            } catch (err) {
                console.warn('findGuestIdFrom error', err);
            }
            return '';
        }

        // If clicking the options button: close all other menus, toggle this one
        const optionsBtn = e.target.closest ? e.target.closest('.player-options-btn') : null;
        if (optionsBtn) {
            document.querySelectorAll('.player-options-menu').forEach(m => m.classList.add('hidden'));
            const wrapper = optionsBtn.closest('.relative');
            const menu = wrapper && wrapper.querySelector('.player-options-menu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
            return;
        }

        // Clicking anywhere outside a menu should close open menus
        if (!e.target.closest || !e.target.closest('.player-options-menu')) {
            document.querySelectorAll('.player-options-menu').forEach(m => m.classList.add('hidden'));
        }

        const closestMenu = e.target.closest ? e.target.closest('.player-options-menu') : null;
        if (closestMenu) closestMenu.classList.add('hidden');

        // Admin: Change Player Role
        const changePlayerBtn = e.target.closest ? e.target.closest('.change-player-role') : null;
        if (changePlayerBtn) {
            const userId = changePlayerBtn.getAttribute('data-user-id');
            const newRole = changePlayerBtn.getAttribute('data-role');
            if (!userId || !newRole) return window.showToast ? window.showToast('Missing data', 'error') : alert('Missing data');
            const body = new URLSearchParams(); body.append('_token', '{{ csrf_token() }}'); body.append('gameRole', newRole);
            fetch(`/admin/game/{{ $game->id }}/${userId}/role`, { method: 'POST', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: body })
                .then(async r => {
                    let json = null;
                    try { json = await r.json(); } catch (err) { }
                    if (r.ok && json && (json.success || json.message)) {
                        return refreshGameSections(json.message || 'Player role updated', 'success');
                    }
                    const msg = (json && (json.error || json.message)) ? (json.error || json.message) : 'Unable to change role';
                    if (window.showToast) window.showToast(msg, 'error'); else alert(msg);
                }).catch(err => { console.error(err); if (window.showToast) window.showToast('Request failed', 'error'); else alert('Request failed'); });
            return;
        }

        // Admin: Remove Player
        const removePlayerBtn = e.target.closest ? e.target.closest('.remove-player') : null;
        if (removePlayerBtn) {
            const userId = removePlayerBtn.getAttribute('data-user-id'); if (!userId) return; if (!confirm('Remove this player?')) return;
            const body = new URLSearchParams(); body.append('_token', '{{ csrf_token() }}'); body.append('userId', userId);
            fetch(`/admin/game/{{ $game->id }}/${userId}/remove`, { method: 'POST', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: body })
                .then(async r => {
                    let json = null;
                    try { json = await r.json(); } catch (err) { }
                    if (r.ok && json && json.success) {
                        return refreshGameSections(json.message || 'Player removed', 'info');
                    }
                    const msg = (json && (json.error || json.message)) ? (json.error || json.message) : 'Unable to remove player';
                    if (window.showToast) window.showToast(msg, 'error'); else alert(msg);
                }).catch(err => { console.error(err); if (window.showToast) window.showToast('Request failed', 'error'); else alert('Request failed'); });
            return;
        }

        const toggleGuestBtn = e.target.closest ? e.target.closest('.toggle-guest-controls') : null;
        if (toggleGuestBtn) {
            const wrapper = toggleGuestBtn.closest('li') || toggleGuestBtn.closest('.relative');
            if (!wrapper) return;
            const controls = wrapper.querySelector('.guest-admin-controls');
            if (controls) controls.classList.toggle('hidden');
            return;
        }

        // Admin: Make Guest Role (Goalie / Player)
        const makeGuestBtn = e.target.closest ? e.target.closest('.make-guest-role') : null;
        if (makeGuestBtn) {
            const menu = makeGuestBtn.closest('.player-options-menu'); if (menu) menu.classList.add('hidden');
            const guestId = findGuestIdFrom(makeGuestBtn);
            const newRole = makeGuestBtn.getAttribute('data-role');
            if (!guestId || !newRole) { if (window.showToast) window.showToast('Missing data', 'error'); else alert('Missing data'); return; }
            const body = new URLSearchParams(); body.append('_token', '{{ csrf_token() }}'); body.append('gameRole', newRole); body.append('guestId', guestId);
            fetch(`/admin/game/{{ $game->id }}/guest/${encodeURIComponent(guestId)}/role`, { method: 'POST', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: body })
                .then(async r => {
                    let json = null;
                    try { json = await r.json(); } catch (err) { }
                    if (r.ok && json && json.success) {
                        return refreshGameSections(json.message || 'Guest role updated', 'success');
                    }
                    const msg = (json && (json.error || json.message)) ? (json.error || json.message) : 'Unable to update guest';
                    if (window.showToast) window.showToast(msg, 'error'); else alert(msg);
                }).catch(err => { console.error(err); if (window.showToast) window.showToast('Request failed', 'error'); else alert('Request failed'); });
            return;
        }

        // Admin: Change Guest Role via select
        const adminChangeGuestBtn = e.target.closest ? e.target.closest('.admin-change-guest') : null;
        if (adminChangeGuestBtn) {
            const guestId = findGuestIdFrom(adminChangeGuestBtn);
            if (!guestId) return;
            const li = adminChangeGuestBtn.closest('li[data-guest-id]'); if (!li) return; const select = li.querySelector('.admin-guest-role-select'); const newRole = select ? select.value : null; if (!newRole) { if (window.showToast) window.showToast('Select a role', 'error'); else alert('Select a role'); return; }
            const menu = adminChangeGuestBtn.closest('.player-options-menu'); if (menu) menu.classList.add('hidden');
            const body = new URLSearchParams(); body.append('_token', '{{ csrf_token() }}'); body.append('gameRole', newRole); body.append('guestId', guestId);
            fetch(`/admin/game/{{ $game->id }}/guest/${encodeURIComponent(guestId)}/role`, { method: 'POST', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: body })
                .then(async r => { 
                    let json = null; 
                    try { json = await r.json(); } catch(e){ } 
                    if (r.ok && json && json.success) {
                        return refreshGameSections(json.message || 'Guest role updated', 'success');
                    }
                    const msg = (json && (json.error || json.message)) ? (json.error || json.message) : 'Unable to update guest'; 
                    if (window.showToast) window.showToast(msg, 'error'); else alert(msg);
                }).catch(err => { console.error(err); if (window.showToast) window.showToast('Request failed', 'error'); else alert('Request failed'); });
            return;
        }

        // Admin: Remove Guest
        const adminRemoveGuestBtn = e.target.closest ? e.target.closest('.admin-remove-guest') : null;
        if (adminRemoveGuestBtn) {
            const guestId = findGuestIdFrom(adminRemoveGuestBtn);
            if (!guestId) return;
            if (!confirm('Remove this guest?')) return;
            const menu = adminRemoveGuestBtn.closest('.player-options-menu'); if (menu) menu.classList.add('hidden');
            const body = new URLSearchParams(); body.append('_token', '{{ csrf_token() }}'); body.append('guestId', guestId);
            fetch(`/admin/game/{{ $game->id }}/guest/${encodeURIComponent(guestId)}/remove`, { method: 'POST', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: body })
                .then(async r => {
                    let json = null;
                    try { json = await r.json(); } catch (err) { }
                    if (r.ok && json && json.success) {
                        return refreshGameSections(json.message || 'Guest removed', 'info');
                    }
                    const msg = (json && (json.error || json.message)) ? (json.error || json.message) : 'Unable to remove guest';
                    if (window.showToast) window.showToast(msg, 'error'); else alert(msg);
                }).catch(err => { console.error(err); if (window.showToast) window.showToast('Request failed', 'error'); else alert('Request failed'); });
            return;
        }

        // Admin: Move Team Member
        const adminMoveTeamBtn = e.target.closest ? e.target.closest('.admin-move-team') : null;
        if (adminMoveTeamBtn) {
            const memberType = adminMoveTeamBtn.getAttribute('data-member-type');
            const memberId = adminMoveTeamBtn.getAttribute('data-member-id');
            const targetTeam = adminMoveTeamBtn.getAttribute('data-target-team');
            if (!memberType || !memberId || !targetTeam) return window.showToast ? window.showToast('Missing data', 'error') : alert('Missing data');
            const body = new URLSearchParams();
            body.append('_token', '{{ csrf_token() }}');
            body.append('memberType', memberType);
            body.append('memberId', memberId);
            body.append('team', targetTeam);
            fetch(`/admin/game/{{ $game->id }}/teams/move`, { method: 'POST', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: body })
                .then(async r => {
                    let json = null;
                    try { json = await r.json(); } catch (err) { }
                    if (r.ok && json && json.success) {
                        return refreshGameSections(json.message || 'Team updated', 'success');
                    }
                    const msg = (json && (json.error || json.message)) ? (json.error || json.message) : 'Unable to move team member';
                    if (window.showToast) window.showToast(msg, 'error'); else alert(msg);
                }).catch(err => { console.error(err); if (window.showToast) window.showToast('Request failed', 'error'); else alert('Request failed'); });
            return;
        }
    });

    // Match map height to sidebar (guest + quick info) on large screens
    (function(){
        const mapWrap = document.getElementById('mapWrap');
        const sidebar = document.getElementById('sidebarCol');
        function matchHeight(){
            if (!mapWrap || !sidebar) return;
            if (window.innerWidth >= 1024) {
                const bringCard = document.getElementById('bringGuestCard');
                if (bringCard) {
                    const mapTop = Math.round(mapWrap.getBoundingClientRect().top);
                    const bringBottom = Math.round(bringCard.getBoundingClientRect().bottom);
                    const h = Math.max(360, bringBottom - mapTop);
                    mapWrap.style.height = h + 'px';
                } else {
                    const h = Math.round(sidebar.getBoundingClientRect().height);
                    mapWrap.style.height = h + 'px';
                }
            } else {
                mapWrap.style.height = '';
            }
        }
        window.matchMapHeight = matchHeight;
        window.addEventListener('resize', function(){ matchHeight(); });
        document.addEventListener('DOMContentLoaded', matchHeight);
        setTimeout(matchHeight, 300);
    })();

    // Silent background poller (every 10 seconds, only when tab is visible and user is not typing)
    setInterval(() => {
        if (document.hidden) return;
        const activeTag = document.activeElement ? document.activeElement.tagName : '';
        if (activeTag === 'INPUT' || activeTag === 'SELECT' || activeTag === 'TEXTAREA') return;
        refreshGameSections();
    }, 10000);

    function liveTeamsReveal(config) {
        return {
            gameId: config.gameId,
            revealTimestamp: config.revealTimestamp,
            ready: Boolean(config.isReady),
            isRevealing: false,
            hours: '00',
            minutes: '00',
            seconds: '00',
            timer: null,
            pollInterval: null,

            initTimer() {
                if (this.ready) return;

                this.updateRemaining();
                this.timer = setInterval(() => {
                    this.updateRemaining();
                }, 1000);
            },

            updateRemaining() {
                const now = Math.floor(Date.now() / 1000);
                const diff = this.revealTimestamp - now;

                if (diff <= 0) {
                    if (this.timer) {
                        clearInterval(this.timer);
                        this.timer = null;
                    }
                    this.hours = '00';
                    this.minutes = '00';
                    this.seconds = '00';
                    this.fetchRoster();
                    return;
                }

                const h = Math.floor(diff / 3600);
                const m = Math.floor((diff % 3600) / 60);
                const s = diff % 60;

                this.hours = String(h).padStart(2, '0');
                this.minutes = String(m).padStart(2, '0');
                this.seconds = String(s).padStart(2, '0');
            },

            fetchRoster() {
                if (this.ready) return;
                this.isRevealing = true;

                fetch(`/game/${this.gameId}/teams-roster`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.ready && data.html) {
                        const container = document.getElementById('teamsRosterContainer');
                        if (container) {
                            container.innerHTML = data.html;
                        }
                        this.ready = true;
                        this.isRevealing = false;
                        if (this.pollInterval) {
                            clearInterval(this.pollInterval);
                            this.pollInterval = null;
                        }
                    } else {
                        // If clock drift, poll every 3 seconds until confirmed
                        if (!this.pollInterval) {
                            this.pollInterval = setInterval(() => {
                                this.fetchRoster();
                            }, 3000);
                        }
                    }
                })
                .catch(err => {
                    console.error('Failed to fetch teams roster:', err);
                    setTimeout(() => this.fetchRoster(), 5000);
                });
            }
        };
    }
</script>
@endpush

</div>

@endsection
