@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto px-4 py-6">
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
        <div class="lg:col-span-3">
            <div class="bg-slate-800 border border-slate-700 rounded-lg p-4 mb-4">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-ice">{{ $game->title }}</h1>
                        @if($game->description)
                            <p class="mt-1 text-slate-300">{{ $game->description }}</p>
                        @endif
                        <div class="mt-3 flex flex-wrap gap-2 text-sm">
                            <div class="flex items-center gap-2 text-slate-300"><svg class="w-4 h-4 stroke-current text-ice" fill="none" viewBox="0 0 24 24" stroke-width="1.5"><path d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z" stroke="currentColor"/></svg><span class="text-ice ml-1">{{ $game->game_time }}</span></div>
                            <div class="flex items-center gap-2 text-slate-300">
                                <!-- stopwatch icon -->
                                <i class="fa-regular fa-hourglass"></i>
                                <span class="text-ice ml-1">{{ $game->duration }} min</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-300">
                                <!-- map-pin icon -->
                                <svg class="w-4 h-4 stroke-current text-ice" viewBox="0 0 24 24" fill="none" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2C8.134 2 5 5.134 5 9c0 5.25 7 12 7 12s7-6.75 7-12c0-3.866-3.134-7-7-7z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="12" cy="9" r="2.2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <a class="text-ice-blue ml-1" href="https://maps.google.com/?q={{ urlencode($game->location) }}" target="_blank">{{ $game->location }}</a>
                            </div>
                            @php
                                $showPrice = auth()->check()
                                    && auth()->user()->role_preference !== \App\Enums\Games\GameRoles::Goalie->value;
                            @endphp
                            @if($showPrice)
                                <div class="flex items-center gap-2 text-slate-300"><span class="text-ice ml-1">${{ $game->price }}</span></div>
                            @endif
                        </div>
                    </div>

                    <div id="gameHeaderActions" class="flex flex-wrap items-center gap-2.5 game-actions">
                        @include('components.add-to-calendar', ['game' => $game])

                        @role('admin')
                            <a href="{{ route('edit_game', ['game' => $game->id]) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-ice-blue text-deep-navy hover:text-deep-navy rounded font-semibold text-xs shadow-sm">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span>Edit Game</span>
                            </a>
                        @endrole

                        {{-- Non-admin attendees can remove themselves from the game. If the user marked cannot-attend, show non-clickable status. --}}
                        @if(auth()->check())
                            @if(!empty($user_registered) && $user_registered)
                                <form method="POST" action="{{ route('game_remove_self', ['game' => $game->id]) }}" data-async-game-form>
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 bg-rose-600 text-white rounded font-semibold">Remove Myself</button>
                                </form>
                            @elseif(!empty($meCannotAttend) && $meCannotAttend)
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-rose-600 text-white rounded font-semibold">Not Attending</span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Main column: Map + details -->
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-lg overflow-hidden border border-slate-700 bg-slate-900">
                <div id="mapWrap" class="w-full flex flex-col">
                    <iframe id="gameMap" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q={{ urlencode($game->location) }}&amp;z=14&amp;output=embed" class="w-full" frameborder="0" marginheight="0" marginwidth="0" loading="lazy"></iframe>

                    <div class="p-3 flex items-center justify-between">
                        <div class="text-sm text-slate-300">Map location</div>
                        <div>
                            <button id="retryMapBtn" type="button" class="px-3 py-1 bg-slate-700 text-ice rounded hidden">Retry Map</button>
                            <a href="https://maps.google.com/?q={{ urlencode($game->location) }}" target="_blank" class="ml-2 text-ice-blue text-sm">Open in Maps</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accept / Guest forms -->
            <div id="acceptGameWrap" class="grid grid-cols-1 gap-4">
                @if($user_registered == false)
                    <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-ice mb-2">Accept Game</h3>
                        <div class="flex flex-wrap gap-2 items-center accept-controls">
                            <form action="{{ route('game_detail_update.game_id', ['game' => $game->id]) }}" method="POST" class="flex-1 min-w-0" data-async-game-form>
                                @csrf
                                <div class="flex gap-2">
                                    <select required name="gameRole" id="gameRole" class="flex-1 min-w-0 bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">
                                        <option value="" selected disabled hidden>Please Select</option>
                                            @foreach ($GAME_ROLES as $gamerole)
                                                @php $isGoalieRole = ($gamerole == App\Enums\Games\GameRoles::Goalie); @endphp
                                                <option value="{{ $gamerole }}" {{ $gamerole == App\Enums\Games\GameRoles::tryFrom(Auth::user()->role_preference) ? 'selected' : '' }} @if($isGoalieRole && $totalGoalies >= 2) disabled title="Goalie roster is full" @endif>{{ $gamerole->name }}</option>
                                            @endforeach
                                    </select>
                                    <button class="px-4 py-2 bg-ice-blue text-deep-navy rounded whitespace-nowrap" type="submit" id="accept_game_submit_button" name="game">Accept</button>
                                </div>
                                @error('gameRole') <div class="text-red-400 text-sm mt-2">{{ $message }}</div> @enderror
                            </form>

                            <form action="{{ route('game_detail_cannot_attend', ['game' => $game->id]) }}" method="POST" class="shrink-0" data-async-game-form>
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded whitespace-nowrap">Cannot Attend</button>
                            </form>
                    <style>
                        @media (max-width: 560px) {
                            /* Stack Accept Game controls vertically on very small screens */
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

                <!-- Bring a Guest removed from main column (moved to sidebar) -->
            </div>

        </div>

        <!-- Sidebar: quick stats -->
        <aside id="sidebarCol" class="space-y-3 lg:self-stretch lg:flex lg:flex-col lg:gap-4 lg:col-span-1">
            <div id="quickInfoCard" class="bg-slate-800/80 border border-ice-blue/25 rounded-xl p-4 shadow-lg shadow-black/20 ring-1 ring-white/5">
                <div class="flex items-center justify-between gap-3">
                    <h4 class="text-base font-semibold text-ice">Quick Info</h4>
                    <span class="text-[11px] uppercase tracking-wide text-slate-400">This game</span>
                </div>

                <div class="mt-3 text-sm">
                    <div class="flex items-center justify-between py-2">
                        <span class="text-slate-300">Players</span>
                        <span class="font-semibold text-ice">{{ count($players) + count($guestPlayers) }}</span>
                    </div>
                    <div class="h-px bg-slate-700/60"></div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-slate-300">Goalies</span>
                        <span class="font-semibold text-ice">{{ count($goalies) + count($guestGoalies) }}</span>
                    </div>
                    @if($showPrice)
                        <div class="h-px bg-slate-700/60"></div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-slate-300">Price</span>
                            <span class="font-semibold text-ice">${{ $game->price }}</span>
                        </div>
                    @endif
                    <div class="h-px bg-slate-700/60"></div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-slate-300">Season</span>
                        <span class="font-semibold text-ice">{{ isset($currentSeason) && $currentSeason ? $currentSeason->season_number : '—' }}</span>
                    </div>
                </div>
            </div>

            <div id="bringGuestCard" class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                <h4 class="text-sm text-slate-300">Bring a Guest</h4>
                <form id="bringGuestForm" action="{{ route('game_detail_update_guest.game_id', ['game' => $game->id]) }}" method="POST" class="mt-2 flex flex-col" data-async-game-form>
                    @csrf
                    <div class="relative">
                        <input type="text" id="guestName" name="guestName" autocomplete="off" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice" placeholder="Guest full name" minlength="4" required>
                        <div id="guestList" class="hidden absolute z-50 w-full mt-1 bg-slate-900 border border-slate-700 rounded shadow-lg max-h-48 overflow-auto"></div>
                    </div>
                    <select required name="gameRole" class="w-full mt-2 bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">
                        <option value="" selected disabled hidden>Position</option>
                        @foreach ($GAME_ROLES as $gamerole)
                            @php $isGoalieRole = ($gamerole == App\Enums\Games\GameRoles::Goalie); @endphp
                            <option value="{{ $gamerole }}" {{ $gamerole == App\Enums\Games\GameRoles::tryFrom(Auth::user()->role_preference) ? 'selected' : '' }} @if($isGoalieRole && $totalGoalies >= 2) disabled title="Goalie roster is full" @endif>{{ $gamerole->name }}</option>
                        @endforeach
                    </select>
                    <label for="level" class="block text-sm font-semibold text-slate-300 mt-3">Guest Level</label>
                    @php
                        $levelDescriptions = [
                            1 => 'Beginner / low rec',
                            2 => 'Recreational',
                            3 => 'Intermediate / competitive',
                            4 => 'Advanced / high skill',
                        ];
                    @endphp
                    <select name="level" id="level" class="w-full mt-1 bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">
                        @for ($i = 1; $i <= 4; $i++)
                            <option value="{{ $i }}" {{ (int) old('level', 2) === $i ? 'selected' : '' }}>{{ $i }} - {{ $levelDescriptions[$i] }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="w-full mt-2 px-4 py-2 bg-ice-blue text-deep-navy rounded">Add</button>
                    @error('guestName') <div class="text-red-400 text-sm mt-2">{{ $message }}</div> @enderror
                </form>
            </div>
        </aside>

        @role('admin')
            <!-- Not Yet Attending (admin only) -->
            <div id="notAttendingWrap" class="lg:col-span-3">
                @if(isset($notAttendingUsers) && $notAttendingUsers->isNotEmpty())
                    <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="text-lg font-semibold text-ice">Not Yet Attending</h3>
                            <span class="text-sm text-slate-300">{{ $notAttendingUsers->count() }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-300">Add a player to this game (admin only).</p>

                        <div class="mt-3 max-h-72 overflow-auto rounded border border-slate-700 bg-slate-900">
                            <ul class="divide-y divide-slate-800">
                                @foreach($notAttendingUsers as $u)
                                    <li class="px-3 py-2 flex items-center justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="text-ice truncate">{{ $u->name }}</div>
                                            <div class="text-xs text-slate-400 truncate">{{ $u->email }}</div>
                                        </div>

                                        <form class="flex items-center gap-2" method="POST" action="{{ route('admin_game_detail_update.game_id.user_id', ['game' => $game->id, 'user_id' => $u->id]) }}" data-async-game-form>
                                            @csrf
                                            <select name="gameRole" class="bg-slate-800 border border-slate-700 rounded px-2 py-1 text-ice text-sm">
                                                @foreach ($GAME_ROLES as $gamerole)
                                                    @php $isGoalieRole = ($gamerole == App\Enums\Games\GameRoles::Goalie); @endphp
                                                    <option value="{{ $gamerole }}" @if($gamerole == App\Enums\Games\GameRoles::Player) selected @endif @if($isGoalieRole && $totalGoalies >= 2) disabled title="Goalie roster is full" @endif>
                                                        {{ $gamerole->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="px-3 py-1.5 bg-ice-blue text-deep-navy rounded text-sm font-semibold">Add</button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @error('gameRole') <div class="text-red-400 text-sm mt-2">{{ $message }}</div> @enderror
                    </div>
                @endif
            </div>
        @endrole

        @role('admin')
            <div id="cannotAttendingWrap" class="lg:col-span-3">
                @if(isset($cannotAttendingUsers) && $cannotAttendingUsers->isNotEmpty())
                    <div class="bg-slate-800 border border-slate-700 rounded-lg p-4 mt-2">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="text-lg font-semibold text-ice">Cannot Attend</h3>
                            <span class="text-sm text-slate-300">{{ $cannotAttendingUsers->count() }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-400">Players who have indicated they cannot attend this game.</p>

                        <div class="mt-3 max-h-56 overflow-auto rounded border border-slate-700 bg-slate-900">
                            <ul class="divide-y divide-slate-800">
                                @foreach($cannotAttendingUsers as $u)
                                    <li class="px-3 py-2 flex items-center justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="text-ice truncate">{{ $u->name }}</div>
                                            <div class="text-xs text-slate-400 truncate">{{ $u->email }}</div>
                                        </div>
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
                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4 mt-2">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-lg font-semibold text-ice">🏒 Email All Players</h3>
                    </div>
                    <p class="mt-1 text-sm text-slate-400">Send an upcoming game reminder to all registered players.</p>

                    <div class="mt-3 flex items-center gap-3">
                        @if($emailMailtoLink)
                            <a href="{{ $emailMailtoLink }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-ice-blue text-deep-navy rounded font-semibold hover:bg-ice-blue/90 hover:text-deep-navy no-underline transition">
                                <i class="fa-solid fa-envelope"></i>
                                <span>Email All Players</span>
                            </a>
                            <span class="text-xs text-slate-400">Opens your email client with all player addresses</span>
                        @else
                            <button disabled class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-700 text-slate-500 rounded font-semibold cursor-not-allowed opacity-60">
                                <i class="fa-solid fa-envelope"></i>
                                <span>Email All Players</span>
                            </button>
                            <span class="text-xs text-slate-400">No players have registered yet</span>
                        @endif
                    </div>
                </div>
            </div>
        @endrole

        <!-- Full width row: Roster + Teams (span to sidebar edge) -->
        <div class="lg:col-span-3 space-y-4">
            <!-- Roster -->
            <div id="attendanceLists" class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-ice mb-3">Roster</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm text-slate-300 mb-2">Goalies</h4>
                        @php
                            // total goalie count includes both user goalies and guest goalies
                            $totalGoalies = count($goalies) + count($guestGoalies);
                        @endphp

                        <ul class="space-y-2">
                            @foreach($goalies as $goalie_id => $goalie_name)
                                <li class="bg-slate-900 border border-slate-700 rounded px-3 py-2 flex items-center justify-between" data-user-id="{{ $goalie_id }}">
                                    <span class="text-ice">{{ $goalie_name }}</span>
                                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                                        <div class="relative">
                                            <button class="player-options-btn px-2 py-1 rounded hover:bg-slate-700">⋮</button>
                                            <div class="player-options-menu hidden absolute right-0 mt-2 w-44 bg-slate-900 border border-slate-700 rounded shadow z-50">
                                                <button data-user-id="{{ $goalie_id }}" data-role="player" class="w-full text-left px-3 py-2 change-player-role">Make Player</button>
                                                <button data-user-id="{{ $goalie_id }}" class="w-full text-left px-3 py-2 remove-player text-rose-500">Remove</button>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                            @foreach($guestGoalies as $guest)
                                <li class="bg-slate-900 border border-slate-700 rounded px-3 py-2 text-slate-300 flex items-center justify-between" data-guest-id="{{ $guest->id ?? '' }}">
                                    <span>{{ $guest->name ?? $guest }}</span>
                                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                                        <div class="relative">
                                            <button class="player-options-btn px-2 py-1 rounded hover:bg-slate-700">⋮</button>
                                            <div class="player-options-menu hidden absolute right-0 mt-2 w-44 bg-slate-900 border border-slate-700 rounded shadow z-50">
                                                <!-- Guest is currently a goalie; offer Make Player -->
                                                <button data-guest-id="{{ $guest->id ?? '' }}" data-role="player" class="w-full text-left px-3 py-2 make-guest-role">Make Player</button>
                                                <button data-guest-id="{{ $guest->id ?? '' }}" class="w-full text-left px-3 py-2 admin-remove-guest text-rose-500">Remove</button>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endforeach

                            @for ($i = $totalGoalies; $i < 2; $i++)
                                <li class="bg-slate-900 border border-slate-700 rounded px-3 py-2 text-slate-500">Empty Net</li>
                            @endfor
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-sm text-slate-300 mb-2">Players</h4>
                        <ul class="space-y-2">
                            @foreach($players as $player_id => $player_name)
                                <li class="bg-slate-900 border border-slate-700 rounded px-3 py-2 flex items-center justify-between" data-user-id="{{ $player_id }}">
                                    <span class="text-ice">{{ $player_name }}</span>
                                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                                        <div class="relative">
                                            <button class="player-options-btn px-2 py-1 rounded hover:bg-slate-700">⋮</button>
                                            <div class="player-options-menu hidden absolute right-0 mt-2 w-44 bg-slate-900 border border-slate-700 rounded shadow z-50">
                                                @php $canMakeGoalie = ($totalGoalies < 2); @endphp
                                                <button data-user-id="{{ $player_id }}" data-role="goalie" class="w-full text-left px-3 py-2 change-player-role hover:bg-slate-700 @if(!$canMakeGoalie) opacity-50 pointer-events-none @endif" @if(!$canMakeGoalie) title="Goalie roster is full" disabled @endif>Make Goalie</button>
                                                <button data-user-id="{{ $player_id }}" class="w-full text-left px-3 py-2 remove-player text-rose-500 hover:bg-slate-700">Remove</button>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endforeach

                            @foreach($guestPlayers as $guest)
                                <li class="bg-slate-900 border border-slate-700 rounded px-3 py-2 text-slate-300 flex items-center justify-between" data-guest-id="{{ $guest->id ?? '' }}">
                                    <span>{{ $guest->name ?? $guest }}</span>
                                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                                        <div class="relative">
                                            <button class="player-options-btn px-2 py-1 rounded hover:bg-slate-700">⋮</button>
                                            <div class="player-options-menu hidden absolute right-0 mt-2 w-44 bg-slate-900 border border-slate-700 rounded shadow z-50">
                                                <!-- Guest is currently a player; offer Make Goalie -->
                                                @php $canMakeGoalieGuest = ($totalGoalies < 2); @endphp
                                                <button data-guest-id="{{ $guest->id ?? '' }}" data-role="goalie" class="w-full text-left px-3 py-2 make-guest-role hover:bg-slate-700 @if(!$canMakeGoalieGuest) opacity-50 pointer-events-none @endif" @if(!$canMakeGoalieGuest) title="Goalie roster is full" disabled @endif>Make Goalie</button>
                                                <button data-guest-id="{{ $guest->id ?? '' }}" class="w-full text-left px-3 py-2 admin-remove-guest text-rose-500 hover:bg-slate-700">Remove</button>
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
                class="bg-slate-800 border border-slate-700 rounded-lg p-5 shadow-lg relative overflow-hidden transition-all duration-300"
                x-data="liveTeamsReveal({
                    gameId: {{ $game->id }},
                    revealTimestamp: {{ $teamsRevealAt->timestamp }},
                    isReady: {{ (!empty($teamsReady) && $teamsReady) ? 'true' : 'false' }}
                })"
                x-init="initTimer()"
            >
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-700/60">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-people-group text-ice-blue text-lg"></i>
                        <h3 class="text-xl font-bold text-ice">Teams</h3>
                    </div>

                    <!-- Live Indicator / Status Badge -->
                    <div>
                        <template x-if="ready">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 border border-emerald-400/30 text-emerald-300 shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                <span>Rosters Revealed</span>
                            </span>
                        </template>

                        <template x-if="!ready">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-ice-blue/10 border border-ice-blue/30 text-ice-blue shadow-sm animate-pulse">
                                <i class="fa-solid fa-clock text-[11px]"></i>
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
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-900 border border-slate-700 text-ice-blue mb-1">
                            <i class="fa-solid fa-stopwatch text-xl" :class="{ 'animate-bounce': isRevealing }"></i>
                        </div>

                        <div>
                            <h4 class="text-base font-semibold text-ice">Team Reveal Countdown</h4>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Teams balance automatically and reveal 30 minutes before puck drop.
                            </p>
                        </div>

                        <!-- Digital Countdown Clock -->
                        <div class="grid grid-cols-3 gap-3 max-w-xs mx-auto py-2">
                            <!-- Hours -->
                            <div class="bg-slate-900/90 border border-slate-700/80 rounded-lg py-2.5 px-2 shadow-inner">
                                <div class="text-2xl sm:text-3xl font-mono font-bold text-ice" x-text="hours">00</div>
                                <div class="text-[10px] tracking-wider uppercase text-slate-400 mt-0.5">Hours</div>
                            </div>
                            <!-- Minutes -->
                            <div class="bg-slate-900/90 border border-slate-700/80 rounded-lg py-2.5 px-2 shadow-inner">
                                <div class="text-2xl sm:text-3xl font-mono font-bold text-ice-blue" x-text="minutes">00</div>
                                <div class="text-[10px] tracking-wider uppercase text-slate-400 mt-0.5">Mins</div>
                            </div>
                            <!-- Seconds -->
                            <div class="bg-slate-900/90 border border-slate-700/80 rounded-lg py-2.5 px-2 shadow-inner">
                                <div class="text-2xl sm:text-3xl font-mono font-bold text-ice" x-text="seconds">00</div>
                                <div class="text-[10px] tracking-wider uppercase text-slate-400 mt-0.5">Secs</div>
                            </div>
                        </div>

                        <!-- Live Status Message -->
                        <div class="text-xs text-slate-400 flex items-center justify-center gap-2">
                            <template x-if="isRevealing">
                                <span class="text-emerald-400 font-medium inline-flex items-center gap-1.5 animate-pulse">
                                    <svg class="animate-spin h-3.5 w-3.5 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    <span>T-30 Reached! Unlocking rosters live...</span>
                                </span>
                            </template>
                            <template x-if="!isRevealing">
                                <span>
                                    Opens at <strong class="text-slate-300">{{ $teamsRevealAt->format('g:i A') }}</strong> • Page will auto-update
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
