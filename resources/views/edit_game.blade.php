@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto">
    <div class="hidden md:flex flex-col justify-center bg-slate-800 p-6 rounded-lg mb-6">
        <h3 class="text-2xl font-semibold text-ice-blue">Edit Game</h3>
        <p class="text-slate-300">Update game details. Changes will apply to the selected season.</p>
    </div>

    <div class="bg-slate-900 border border-slate-700 rounded-lg shadow-md px-6 py-8">
        <form method="POST" action="{{ route('game_edit', ['game' => $game->id]) }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm text-slate-300">Title</label>
                <input name="title" type="text" value="{{ old('title', $game->title) }}"
                    class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" placeholder="Game 1">
                @error('title') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-slate-300">Date</label>
                    <div class="relative input-with-icon mt-1">
                        <input name="date" type="date" value="{{ old('date', $game_date) }}" required
                            class="w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue">
                        <span class="input-icon text-ice">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 stroke-current text-ice" viewBox="0 0 24 24" fill="none" stroke-width="1.5">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </span>
                    </div>
                    @error('date') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm text-slate-300">Time</label>
                    <div class="relative input-with-icon mt-1">
                        <input name="time" type="time" value="{{ old('time', $game_time) }}" required
                            class="w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue">
                        <span class="input-icon text-ice">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 stroke-current text-ice" viewBox="0 0 24 24" fill="none" stroke-width="1.5">
                                <circle cx="12" cy="12" r="9"></circle>
                                <polyline points="12 7 12 12 15 15"></polyline>
                            </svg>
                        </span>
                    </div>
                    @error('time') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm text-slate-300">Duration (min)</label>
                    <input name="duration" type="number" min="1" value="{{ old('duration', $game->duration ?? 50) }}"
                        class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue">
                    @error('duration') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm text-slate-300">Location</label>
                <input name="location" type="text" value="{{ old('location', $game->location) }}"
                    class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" placeholder="Address or rink name">
                @error('location') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-sm text-slate-300">Price Per Player</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-md bg-slate-800 border border-r-0 border-slate-700 text-slate-300">$</span>
                        <input name="price" type="number" step="0.01" min="0" value="{{ old('price', $game->price) }}"
                            class="w-full bg-slate-800 text-gray-100 border border-slate-700 rounded-r-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue">
                    </div>
                    @error('price') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm text-slate-300">Season</label>
                    <select name="season" class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue">
                        @if (count($seasons) == 0)
                            <option value="" disabled>Create a season first</option>
                        @else
                            @foreach ($seasons as $season)
                                <option value="{{ $season->id }}" {{ (old('season') ?? $game->season_id) == $season->id ? 'selected' : '' }}>Season {{ $season->season_number }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <button type="submit" class="w-full bg-ice-blue text-deep-navy font-semibold py-2 rounded shadow">Update Game</button>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('games.index') }}" class="w-full inline-block text-center bg-transparent border border-slate-700 text-slate-200 py-2 rounded">Cancel</a>
                <a href="{{ route('delete_game', ['game' => $game->id]) }}" onclick="return confirm('Are you sure you want to delete this game?');" class="w-full inline-block text-center bg-rose-600 text-white py-2 rounded">Delete</a>
            </div>
        </form>
    </div>

    <!-- Add New Season Modal -->
    <div class="modal fade" id="addNewSeasonModal" tabindex="-1" role="dialog" aria-labelledby="addNewSeasonModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content bg-slate-900 border border-slate-700">
                <div class="modal-header">
                    <h5 class="modal-title text-ice" id="addNewSeasonModalLabel">Add New Season</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addNewSeasonForm" action="{{ route('season.create') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label for="season_number" class="block text-sm text-slate-300">Season Number</label>
                        <input type="number" class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2" id="season_number" name="season_number" min="1" value="{{ $nextSeasonNumber }}" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="bg-ice-blue text-deep-navy px-4 py-2 rounded">Add Season</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
        el.style.cursor = 'pointer';

        const openPicker = function () {
            if (typeof el.showPicker === 'function') {
                try { el.showPicker(); } catch (e) { el.focus(); }
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
</script>
@endpush

@endsection
