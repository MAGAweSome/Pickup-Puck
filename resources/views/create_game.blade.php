@extends('layouts.app')

@section('content')

<div class="h-full flex items-center py-6">
    <div class="w-full max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="hidden md:flex flex-col justify-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-8 rounded-2xl shadow-md">
        <h3 class="text-2xl font-bold text-slate-900 dark:text-ice-blue mb-2">Create Game</h3>
        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">Add a new pickup game — set time, location, price and season. Use the form to the right to create.</p>
    </div>

    <div class="md:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl px-6 py-8">
        <style>
            /* Hide native date/time picker indicators across browsers and keep inputs clickable */
            input[type="date"], input[type="time"] {
                -webkit-appearance: none;
                -moz-appearance: textfield;
                appearance: none;
                background-color: transparent;
            }
            input[type="date"]::-webkit-calendar-picker-indicator,
            input[type="time"]::-webkit-calendar-picker-indicator {
                display: none;
            }
            /* Ensure extra space for our custom icon */
            .input-with-icon { position: relative; }
            .input-with-icon input { padding-right: 2.75rem; }
            .input-with-icon .input-icon { position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: inherit; }
            .input-with-icon .input-icon svg { display:block; }
        </style>
        <form method="POST" action="{{ route('game_create') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">Title</label>
                <input name="title" type="text" value="{{ old('title', $defaults['suggested_title'] ?? '') }}"
                    class="mt-1 w-full bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-gray-100 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 dark:focus:ring-ice-blue" placeholder="Game 1">
                @error('title') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">Date</label>
                    <div class="relative input-with-icon mt-1">
                        <input name="date" type="date" value="{{ old('date') }}" required
                            class="w-full bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-gray-100 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 dark:focus:ring-ice-blue">
                        <span class="input-icon text-sky-600 dark:text-ice">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 stroke-current" viewBox="0 0 24 24" fill="none" stroke-width="1.5">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </span>
                    </div>
                    @error('date') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">Time</label>
                    <div class="relative input-with-icon mt-1">
                        <input name="time" type="time" value="{{ old('time', isset($defaults['time']) ? \Carbon\Carbon::parse($defaults['time'])->format('H:i') : '') }}" required
                            class="w-full bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-gray-100 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 dark:focus:ring-ice-blue">
                        <span class="input-icon text-sky-600 dark:text-ice">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 stroke-current" viewBox="0 0 24 24" fill="none" stroke-width="1.5">
                                <circle cx="12" cy="12" r="9"></circle>
                                <polyline points="12 7 12 12 15 15"></polyline>
                            </svg>
                        </span>
                    </div>
                    @error('time') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">Duration (min)</label>
                    <input name="duration" type="number" min="1" value="{{ old('duration', $defaults['duration'] ?? 50) }}" required
                        class="mt-1 w-full bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-gray-100 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 dark:focus:ring-ice-blue">
                    @error('duration') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">Location</label>
                <input name="location" type="text" value="{{ old('location', $defaults['location'] ?? '1001 Franklin Blvd, Cambridge, ON N1R 8B5') }}" required
                    class="mt-1 w-full bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-gray-100 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 dark:focus:ring-ice-blue" placeholder="Address or rink name">
                @error('location') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">Price Per Player</label>
                    <div class="mt-1 flex rounded-xl shadow-sm">
                        <span class="inline-flex items-center px-3.5 rounded-l-xl bg-slate-100 dark:bg-slate-800 border border-r-0 border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold">$</span>
                        <input name="price" type="number" step="0.01" min="0" value="{{ old('price', $defaults['price'] ?? 15) }}" required
                            class="w-full bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-gray-100 border border-slate-300 dark:border-slate-700 rounded-r-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 dark:focus:ring-ice-blue">
                    </div>
                    @error('price') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">Season</label>
                    <select id="season_select" name="season" class="mt-1 w-full bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-gray-100 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 dark:focus:ring-ice-blue cursor-pointer">
                        <option value="">(Choose a season)</option>
                        @if (count($seasons) == 0)
                            <option value="" disabled>Create a season first</option>
                        @else
                            @foreach ($seasons as $season)
                                @php
                                    $selectedSeason = old('season') ?? ($defaults['season_id'] ?? $currentSeasonId ?? null);
                                @endphp
                                <option value="{{ $season->id }}" {{ $selectedSeason == $season->id ? 'selected' : '' }}>Season {{ $season->season_number }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('season') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <button type="button" id="btnAddNewSeason" onclick="autoCreateSeason()" class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm font-semibold transition flex items-center justify-center gap-2 shadow-sm">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Add New Season</span>
                    </button>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="w-full bg-gradient-to-r from-sky-600 to-indigo-600 dark:from-cyan-500 dark:to-sky-600 text-white dark:text-slate-950 font-bold py-2.5 rounded-xl shadow-md transition">Create Game</button>
                <a href="{{ route('home') }}" class="w-full inline-block text-center bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 py-2.5 rounded-xl font-semibold no-underline transition">Cancel</a>
            </div>
        </form>
    </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inject cross-browser CSS to hide native indicators and reserve icon space
    const style = document.createElement('style');
    style.innerHTML = `
        input[type="date"], input[type="time"] {
            -webkit-appearance: none;
            -moz-appearance: textfield;
            appearance: none;
            background-color: transparent;
        }
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
                try { el.showPicker(); } catch (e) { /* ignore */ }
            } else { el.focus(); }
        };

        wrapper.addEventListener('click', function(e){
            if (e.target && (e.target.tagName === 'INPUT' || e.target.closest('input'))) return;
            openPicker();
        });

        el.addEventListener('click', openPicker);
        el.addEventListener('focus', openPicker);
    });
});

window.autoCreateSeason = async function() {
    const btn = document.getElementById('btnAddNewSeason');
    const originalHtml = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> <span>Adding...</span>';
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const res = await fetch('{{ route('season.create') }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        });

        const data = await res.json();
        if (data.success && data.season) {
            const select = document.getElementById('season_select') || document.querySelector('select[name="season"]');
            if (select) {
                const emptyOption = select.querySelector('option[disabled]');
                if (emptyOption) emptyOption.remove();

                const option = document.createElement('option');
                option.value = data.season.id;
                option.textContent = 'Season ' + data.season.season_number;
                option.selected = true;
                select.appendChild(option);
            }
            if (window.showToast) {
                window.showToast(`Season ${data.season.season_number} added and selected!`, 'success');
            }
        } else {
            alert('Failed to add season: ' + (data.message || 'Unknown error'));
        }
    } catch (err) {
        console.error(err);
        alert('Network error adding new season.');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }
};
</script>
@endpush
