@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-slate-100">Application Settings</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-400">Configure global defaults for new game sessions and scheduling preferences.</p>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-sm font-semibold flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('settings.update') }}" method="POST" class="bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 rounded-3xl p-6 sm:p-8 shadow-sm dark:shadow-md space-y-6">
            @csrf
            <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 dark:border-slate-800">
                <i class="fa-solid fa-sliders text-sky-600 dark:text-sky-400"></i>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Game Creation Defaults</h3>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Title Template</label>
                <input type="text" name="default_title_template" value="{{ old('default_title_template', $defaults['title_template'] ?? '') }}" placeholder="e.g. Game {n} or Pickup {n}" class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 transition shadow-inner">
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">Use <code class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 font-mono text-[11px] text-sky-600 dark:text-sky-400">{n}</code> where the incrementing game number should appear.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Default Time</label>
                    <div class="relative input-with-icon">
                        <input type="time" id="default_time" name="default_time" value="{{ old('default_time', isset($defaults['time']) ? \Carbon\Carbon::parse($defaults['time'])->format('H:i') : '') }}" class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 transition shadow-inner">
                        <span class="input-icon text-slate-400 dark:text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" viewBox="0 0 24 24" fill="none" stroke-width="2">
                                <circle cx="12" cy="12" r="9"></circle>
                                <polyline points="12 7 12 12 15 15"></polyline>
                            </svg>
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Default Duration (min)</label>
                    <input type="number" name="default_duration" min="1" value="{{ old('default_duration', $defaults['duration'] ?? '') }}" class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 transition shadow-inner">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Default Location</label>
                    <input type="text" name="default_location" value="{{ old('default_location', $defaults['location'] ?? '') }}" class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 transition shadow-inner">
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Default Price</label>
                    <div class="flex rounded-xl shadow-inner overflow-hidden">
                        <span class="inline-flex items-center px-3.5 bg-slate-100 dark:bg-slate-800 border border-r-0 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm font-bold">$</span>
                        <input type="number" step="0.01" min="0" name="default_price" value="{{ old('default_price', $defaults['price'] ?? '') }}" class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-r-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Default Season</label>
                    <select name="default_season_id" class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 transition shadow-inner">
                        <option value="">(Use no default)</option>
                        @foreach($seasons as $season)
                            <option value="{{ $season->id }}" {{ old('default_season_id', $defaults['season_id'] ?? '') == $season->id ? 'selected' : '' }}>Season {{ $season->season_number }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 dark:from-sky-500 dark:to-cyan-500 text-white dark:text-slate-950 font-bold rounded-xl text-sm shadow-md transition">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Save Settings</span>
                </button>
            </div>
        </form>

        <!-- Season Management Section -->
        <div class="bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 rounded-3xl p-6 sm:p-8 shadow-sm dark:shadow-md space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-calendar-check text-sky-600 dark:text-sky-400"></i>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Season Management</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage existing seasons, inspect attached games, or delete unused seasons.</p>
                    </div>
                </div>

                @if(isset($currentSeason) && $currentSeason)
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-sky-50 dark:bg-sky-500/15 border border-sky-200 dark:border-sky-500/30 text-sky-700 dark:text-sky-300 text-xs font-mono font-bold self-start sm:self-auto">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Current Active: Season {{ $currentSeason->season_number }}</span>
                    </div>
                @else
                    <span class="text-xs font-mono text-slate-400">No active season</span>
                @endif
            </div>

            @if($seasons->isEmpty())
                <div class="p-6 text-center rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 text-xs">
                    No seasons have been created yet. When you create a game, a new season will automatically be assigned.
                </div>
            @else
                <div class="space-y-4">
                    <div>
                        <label for="season_select_edit" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Select Season to Edit / Delete
                        </label>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <div class="relative flex-1">
                                <select id="season_select_edit" onchange="updateSeasonPreview()" class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 transition shadow-inner appearance-none cursor-pointer">
                                    @foreach($seasons as $s)
                                        <option value="{{ $s->id }}" 
                                            data-season-number="{{ $s->season_number }}"
                                            data-games-count="{{ $s->games_count }}"
                                            {{ (isset($currentSeason) && $currentSeason->id == $s->id) ? 'selected' : '' }}>
                                            Season {{ $s->season_number }} ({{ $s->games_count }} {{ Str::plural('game', $s->games_count) }}){{ (isset($currentSeason) && $currentSeason->id == $s->id) ? ' — Current' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            </div>

                            <button type="button" id="btnTriggerDeleteSeason" onclick="openSeasonDeleteModal()" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/15 dark:hover:bg-rose-500/25 border border-rose-300 dark:border-rose-500/40 text-rose-700 dark:text-rose-300 rounded-xl text-xs font-bold transition shadow-sm shrink-0">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                                <span id="lblDeleteBtn">Delete Season</span>
                            </button>
                        </div>
                    </div>

                    <!-- Season Context Card -->
                    <div id="seasonPreviewCard" class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-3">
                            <div id="seasonPillIcon" class="w-9 h-9 rounded-xl bg-sky-100 dark:bg-sky-500/20 text-sky-600 dark:text-sky-400 flex items-center justify-center font-black text-xs font-mono">
                                S1
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 dark:text-slate-100 text-sm" id="seasonPreviewTitle">Season 1</div>
                                <p class="text-slate-500 dark:text-slate-400 text-xs" id="seasonPreviewDescription">Loading details...</p>
                            </div>
                        </div>
                        <div id="seasonPreviewBadge" class="font-mono font-bold text-[11px] px-3 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 self-start sm:self-auto">
                            0 Games
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Confirm Season Deletion Modal -->
    <div id="seasonDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/75 backdrop-blur-sm p-4 hidden">
        <div class="bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 rounded-3xl p-6 sm:p-7 max-w-lg w-full shadow-2xl space-y-5 transform transition-all">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-base shrink-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-slate-100" id="seasonModalTitle">Delete Season</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Please confirm this irreversible action.</p>
                    </div>
                </div>
                <button type="button" onclick="closeSeasonDeleteModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition text-lg leading-none p-1">
                    &times;
                </button>
            </div>

            <div id="seasonModalWarning" class="space-y-3 text-xs">
                <!-- Injected via JavaScript -->
            </div>

            <form id="seasonDeleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeSeasonDeleteModal()" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit" id="seasonModalSubmitBtn" class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs shadow-md transition">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                        <span>Delete Season</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Add cross-browser rules (appearance none) and ensure icon space; then wire openPicker on wrappers and inputs
    const style = document.createElement('style');
    style.innerHTML = `
        /* remove native appearance but keep background so inputs match site styling */
        input[type="date"], input[type="time"] {
            -webkit-appearance: none;
            -moz-appearance: textfield;
            appearance: none;
        }
        /* hide the native picker icon for WebKit browsers */
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="time"]::-webkit-calendar-picker-indicator { display: none; }
        .input-with-icon { position: relative; }
        .input-with-icon input { padding-right: 2.75rem; }
        .input-with-icon .input-icon { position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: inherit; }
    `;
    document.head.appendChild(style);

    const wrappers = document.querySelectorAll('.input-with-icon');
    wrappers.forEach(function(wrapper){
        const el = wrapper.querySelector('input[type="date"], input[type="time"]');
        if (!el) return;
        // Make cursor indicate clickable
        el.style.cursor = 'pointer';

        const openPicker = function () {
            if (typeof el.showPicker === 'function') {
                try { el.showPicker(); } catch (e) { el.focus(); }
            } else { el.focus(); }
        };

        // Click on wrapper (including on icon area) opens picker
        wrapper.addEventListener('click', function(e){
            // Ignore if clicking and interacting with other controls
            if (e.target && (e.target.tagName === 'INPUT' || e.target.closest('input'))) return;
            openPicker();
        });

        el.addEventListener('click', openPicker);
        el.addEventListener('focus', openPicker);
    });

    // Initialize season preview on page load
    updateSeasonPreview();
});

window.updateSeasonPreview = function() {
    const select = document.getElementById('season_select_edit');
    if (!select || !select.value) return;

    const opt = select.options[select.selectedIndex];
    if (!opt) return;

    const seasonNumber = opt.getAttribute('data-season-number');
    const gamesCount = parseInt(opt.getAttribute('data-games-count') || '0', 10);

    const titleEl = document.getElementById('seasonPreviewTitle');
    const descEl = document.getElementById('seasonPreviewDescription');
    const badgeEl = document.getElementById('seasonPreviewBadge');
    const iconEl = document.getElementById('seasonPillIcon');
    const btnText = document.getElementById('lblDeleteBtn');

    if (titleEl) titleEl.innerText = `Season ${seasonNumber}`;
    if (iconEl) iconEl.innerText = `S${seasonNumber}`;
    if (btnText) btnText.innerText = `Delete Season ${seasonNumber}`;

    if (badgeEl) {
        badgeEl.innerText = `${gamesCount} ${gamesCount === 1 ? 'Game' : 'Games'}`;
        if (gamesCount > 0) {
            badgeEl.className = "font-mono font-bold text-[11px] px-3 py-1 rounded-lg bg-amber-50 dark:bg-amber-500/15 border border-amber-300 dark:border-amber-500/30 text-amber-800 dark:text-amber-300 self-start sm:self-auto";
        } else {
            badgeEl.className = "font-mono font-bold text-[11px] px-3 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 self-start sm:self-auto";
        }
    }

    if (descEl) {
        if (gamesCount > 0) {
            descEl.innerText = `Contains ${gamesCount} scheduled game(s) with active player rosters and attendance records.`;
        } else {
            descEl.innerText = `No games attached. Safe to delete without impacting game history.`;
        }
    }
};

window.openSeasonDeleteModal = function() {
    const select = document.getElementById('season_select_edit');
    if (!select || !select.value) return;

    const opt = select.options[select.selectedIndex];
    const seasonId = opt.value;
    const seasonNumber = opt.getAttribute('data-season-number') || '';
    const gamesCount = parseInt(opt.getAttribute('data-games-count') || '0', 10);

    const form = document.getElementById('seasonDeleteForm');
    form.action = `/admin/seasons/${seasonId}`;

    const modalTitle = document.getElementById('seasonModalTitle');
    const modalWarning = document.getElementById('seasonModalWarning');
    const modalSubmitBtn = document.getElementById('seasonModalSubmitBtn');

    if (gamesCount > 0) {
        modalTitle.innerText = `Delete Season ${seasonNumber} & All Game Contents`;
        modalWarning.innerHTML = `
            <div class="p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-500/15 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-xs font-medium space-y-2">
                <div class="font-bold flex items-center gap-1.5 text-rose-900 dark:text-rose-200">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400"></i>
                    <span>Attached Games &amp; Rosters Warning</span>
                </div>
                <p>Season <strong>${seasonNumber}</strong> currently has <strong>${gamesCount} game(s)</strong> attached from the roster.</p>
                <p class="font-semibold text-rose-900 dark:text-rose-200">Deleting this season will permanently remove Season ${seasonNumber} and all of its game contents, including rosters, player signups, and scores.</p>
            </div>
            <p class="text-xs text-slate-600 dark:text-slate-400 mt-2">Are you sure you want to proceed with deleting Season ${seasonNumber} and all its game contents?</p>
        `;
        modalSubmitBtn.innerHTML = `<i class="fa-solid fa-trash-can text-xs"></i> <span>Confirm Deletion of Season &amp; All Game Contents</span>`;
    } else {
        modalTitle.innerText = `Delete Season ${seasonNumber}`;
        modalWarning.innerHTML = `
            <div class="p-3.5 rounded-2xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs">
                <p>Season <strong>${seasonNumber}</strong> has no games attached. Are you sure you want to delete this season?</p>
            </div>
        `;
        modalSubmitBtn.innerHTML = `<i class="fa-solid fa-trash-can text-xs"></i> <span>Delete Season</span>`;
    }

    const modal = document.getElementById('seasonDeleteModal');
    if (modal) modal.classList.remove('hidden');
};

window.closeSeasonDeleteModal = function() {
    const modal = document.getElementById('seasonDeleteModal');
    if (modal) modal.classList.add('hidden');
};
</script>
@endpush
