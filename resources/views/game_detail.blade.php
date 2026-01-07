@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto px-4 py-6">
    @php
        // global total goalies used by forms and admin buttons
        $totalGoalies = (isset($goalies) ? count($goalies) : 0) + (isset($guestGoalies) ? count($guestGoalies) : 0);
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
                            <div class="flex items-center gap-2 text-slate-300"><span class="text-ice ml-1">${{ $game->price }}</span></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- <a href="{{ route('games.index') }}" class="px-3 py-1 border border-slate-700 rounded text-slate-200">Back</a> -->
                        @can('update', $game)
                            <a href="{{ route('game_edit', ['game' => $game->id]) }}" class="px-3 py-1 bg-slate-700 text-ice rounded">Edit</a>
                        @endcan
                        @can('delete', $game)
                            <form action="{{ route('delete_game', ['game' => $game->id]) }}" method="POST" onsubmit="return confirm('Delete this game?');">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-rose-600 text-white rounded">Delete</button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        <!-- Main column: Map + details -->
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-lg overflow-hidden border border-slate-700 bg-slate-900">
                <div id="mapWrap" class="w-full flex flex-col">
                    <iframe id="gameMap" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q={{ urlencode($game->location) }}&amp;z=14&amp;output=embed" class="w-full flex-1" frameborder="0" marginheight="0" marginwidth="0" loading="lazy"></iframe>

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
            <div class="grid md:grid-cols-2 gap-4">
                @if($user_registered == false)
                    <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-ice mb-2">Accept Game</h3>
                        <form action="{{ route('game_detail_update.game_id', ['game' => $game->id]) }}" method="POST">
                            @csrf
                            <div class="flex gap-2">
                                <select required name="gameRole" id="gameRole" class="flex-1 bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">
                                    <option value="" selected disabled hidden>Please Select</option>
                                        @foreach ($GAME_ROLES as $gamerole)
                                            @php $isGoalieRole = ($gamerole == App\Enums\Games\GameRoles::Goalie); @endphp
                                            <option value="{{ $gamerole }}" {{ $gamerole == App\Enums\Games\GameRoles::tryFrom(Auth::user()->role_preference) ? 'selected' : '' }} @if($isGoalieRole && $totalGoalies >= 2) disabled title="Goalie roster is full" @endif>{{ $gamerole->name }}</option>
                                        @endforeach
                                </select>
                                <button class="px-4 py-2 bg-ice-blue text-deep-navy rounded" type="submit" id="accept_game_submit_button" name="game">Accept</button>
                            </div>
                            @error('gameRole') <div class="text-red-400 text-sm mt-2">{{ $message }}</div> @enderror
                        </form>
                    </div>
                @endif

                <!-- Bring a Guest removed from main column (moved to sidebar) -->
            </div>

        </div>

        <!-- Sidebar: quick stats -->
        <aside id="sidebarCol" class="space-y-3 lg:self-stretch lg:flex lg:flex-col lg:gap-4 lg:col-span-1">
            <div class="bg-slate-800 border border-slate-700 rounded-lg p-3 mb-1">
                <h4 class="text-sm text-slate-300">Quick Info</h4>
                <div class="mt-2 text-ice text-sm space-y-2">
                    <div class="flex justify-between"><span class="text-slate-300">Players</span><span>{{ count($players) + count($guestPlayers) }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-300">Goalies</span><span>{{ count($goalies) + count($guestGoalies) }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-300">Price</span><span>${{ $game->price }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-300">Season</span><span>{{ isset($currentSeason) && $currentSeason ? $currentSeason->season_number : '—' }}</span></div>
                </div>
            </div>

            <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                <h4 class="text-sm text-slate-300">Bring a Guest</h4>
                <form action="{{ route('game_detail_update_guest.game_id', ['game' => $game->id]) }}" method="POST" class="mt-2 flex flex-col">
                    @csrf
                    <input type="text" id="guestName" name="guestName" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice" placeholder="Guest full name" minlength="4" required>
                    <select required name="gameRole" class="w-full mt-2 bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">
                        <option value="" selected disabled hidden>Position</option>
                        @foreach ($GAME_ROLES as $gamerole)
                            @php $isGoalieRole = ($gamerole == App\Enums\Games\GameRoles::Goalie); @endphp
                            <option value="{{ $gamerole }}" {{ $gamerole == App\Enums\Games\GameRoles::tryFrom(Auth::user()->role_preference) ? 'selected' : '' }} @if($isGoalieRole && $totalGoalies >= 2) disabled title="Goalie roster is full" @endif>{{ $gamerole->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full mt-2 px-4 py-2 bg-ice-blue text-deep-navy rounded">Add</button>
                    @error('guestName') <div class="text-red-400 text-sm mt-2">{{ $message }}</div> @enderror
                    <div id="guestList" class="mt-2"></div>
                </form>
            </div>
        </aside>

        <!-- Full width row: Roster + Teams (span to sidebar edge) -->
        <div class="lg:col-span-3 space-y-4">
            <!-- Roster -->
            <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
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
        

@push('scripts')
<script>
    // Guest search and list (uses existing endpoints)
    $(document).ready(function(){
        $('#guestName').on('keyup', function(){
            var value = $(this).val();
            $.ajax({ url: "{{$game->id}}/search", type: "GET", data: {'guestName':value}, success: function(data){ $('#guestList').html(data); } });
        });

        // Only handle clicks on search result items inside #guestList
        $(document).on('click', '#guestList li', function(e){
            e.stopPropagation();
            var value = $(this).text().trim();
            $('#guestName').val(value);
            $('#guestList').html('');
        });
    });

    // Player options toggle and role/guest admin actions retained
    document.addEventListener('click', function (e) {
        // helper to robustly find guest id from various click targets
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
                // look for ancestor .relative then its li
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

        // Helper: hide the closest menu for the clicked element
        const closestMenu = e.target.closest ? e.target.closest('.player-options-menu') : null;
        if (closestMenu) closestMenu.classList.add('hidden');

        const changePlayerBtn = e.target.closest ? e.target.closest('.change-player-role') : null;
        if (changePlayerBtn) {
            const userId = changePlayerBtn.getAttribute('data-user-id');
            const newRole = changePlayerBtn.getAttribute('data-role');
            if (!userId || !newRole) return alert('Missing data');
            const body = new URLSearchParams(); body.append('_token', '{{ csrf_token() }}'); body.append('gameRole', newRole);
            fetch(`/admin/game/{{ $game->id }}/${userId}/role`, { method: 'POST', headers: { 'Accept': 'application/json' }, body: body })
                .then(async r => {
                    let json = null;
                    try { json = await r.json(); } catch (err) { /* ignore parse errors */ }
                    if (r.ok && json && (json.success || json.message)) return location.reload();
                    const msg = (json && (json.error || json.message)) ? (json.error || json.message) : 'Unable to change role';
                    alert(msg);
                }).catch(err => { console.error(err); alert('Request failed'); });
            return;
        }

        const removePlayerBtn = e.target.closest ? e.target.closest('.remove-player') : null;
        if (removePlayerBtn) {
            const userId = removePlayerBtn.getAttribute('data-user-id'); if (!userId) return; if (!confirm('Remove this player?')) return;
            const body = new URLSearchParams(); body.append('_token', '{{ csrf_token() }}'); body.append('userId', userId);
            fetch(`/admin/game/{{ $game->id }}/${userId}/remove`, { method: 'POST', headers: { 'Accept': 'application/json' }, body: body }).then(r => r.json()).then(json => { if (json && json.success) location.reload(); else alert('Unable to remove player'); }).catch(err => { console.error(err); alert('Request failed'); });
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

        // Admin: make a guest a specific role (Make Player / Make Goalie)
        const makeGuestBtn = e.target.closest ? e.target.closest('.make-guest-role') : null;
        if (makeGuestBtn) {
            // hide the menu immediately
            const menu = makeGuestBtn.closest('.player-options-menu'); if (menu) menu.classList.add('hidden');
            // robustly find guest id
            const guestId = findGuestIdFrom(makeGuestBtn);
            console.debug('makeGuestBtn clicked', { guestId, el: makeGuestBtn });
            const newRole = makeGuestBtn.getAttribute('data-role');
            if (!guestId || !newRole) { alert('Missing data'); console.debug('makeGuestBtn missing', {guestId, newRole, el: makeGuestBtn}); return; }
            const body = new URLSearchParams(); body.append('_token', '{{ csrf_token() }}'); body.append('gameRole', newRole); body.append('guestId', guestId);
            fetch(`/admin/game/{{ $game->id }}/guest/${encodeURIComponent(guestId)}/role`, { method: 'POST', headers: { 'Accept': 'application/json' }, body: body })
                .then(async r => {
                    let json = null;
                    try { json = await r.json(); } catch (err) { console.warn('non-json response', err); }
                    if (r.ok && json && json.success) return location.reload();
                    const msg = (json && (json.error || json.message)) ? (json.error || json.message) : 'Unable to update guest';
                    alert(msg);
                }).catch(err => { console.error(err); alert('Request failed'); });
            return;
        }

        const adminChangeGuestBtn = e.target.closest ? e.target.closest('.admin-change-guest') : null;
        if (adminChangeGuestBtn) {
            const guestId = findGuestIdFrom(adminChangeGuestBtn);
            console.debug('adminChangeGuestBtn clicked', { guestId, el: adminChangeGuestBtn });
            if (!guestId) { console.debug('adminChangeGuestBtn no guestId', adminChangeGuestBtn); return; }
            const li = adminChangeGuestBtn.closest('li[data-guest-id]'); if (!li) return; const select = li.querySelector('.admin-guest-role-select'); const newRole = select ? select.value : null; if (!newRole) return alert('Select a role');
            const menu = adminChangeGuestBtn.closest('.player-options-menu'); if (menu) menu.classList.add('hidden');
            const body = new URLSearchParams(); body.append('_token', '{{ csrf_token() }}'); body.append('gameRole', newRole); body.append('guestId', guestId);
            fetch(`/admin/game/{{ $game->id }}/guest/${encodeURIComponent(guestId)}/role`, { method: 'POST', headers: { 'Accept': 'application/json' }, body: body }).then(async r => { let json = null; try { json = await r.json(); } catch(e){ } if (r.ok && json && json.success) return location.reload(); const msg = (json && (json.error || json.message)) ? (json.error || json.message) : 'Unable to update guest'; alert(msg); }).catch(err => { console.error(err); alert('Request failed'); });
            return;
        }

        const adminRemoveGuestBtn = e.target.closest ? e.target.closest('.admin-remove-guest') : null;
        if (adminRemoveGuestBtn) {
            const guestId = findGuestIdFrom(adminRemoveGuestBtn);
            console.debug('adminRemoveGuestBtn clicked', { guestId, el: adminRemoveGuestBtn });
            if (!guestId) { console.debug('adminRemoveGuestBtn no guestId', adminRemoveGuestBtn); return; }
            if (!confirm('Remove this guest?')) return;
            const menu = adminRemoveGuestBtn.closest('.player-options-menu'); if (menu) menu.classList.add('hidden');
            const body = new URLSearchParams(); body.append('_token', '{{ csrf_token() }}'); body.append('guestId', guestId);
            fetch(`/admin/game/{{ $game->id }}/guest/${encodeURIComponent(guestId)}/remove`, { method: 'POST', headers: { 'Accept': 'application/json' }, body: body })
                .then(async r => {
                    let json = null;
                    try { json = await r.json(); } catch (err) { console.warn('non-json response', err); }
                    if (r.ok && json && json.success) return location.reload();
                    const msg = (json && (json.error || json.message)) ? (json.error || json.message) : 'Unable to remove guest';
                    alert(msg);
                }).catch(err => { console.error(err); alert('Request failed'); });
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
                const h = Math.round(sidebar.getBoundingClientRect().height);
                mapWrap.style.height = h + 'px';
            } else {
                mapWrap.style.height = '';
            }
        }
        window.addEventListener('resize', function(){ matchHeight(); });
        document.addEventListener('DOMContentLoaded', matchHeight);
        // try run after a short delay to allow fonts/images to settle
        setTimeout(matchHeight, 300);
    })();
</script>
@endpush

</div>

@endsection
