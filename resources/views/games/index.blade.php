@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold">Games</h1>

            <div class="flex items-center gap-4">
                @if(isset($seasons) && $seasons->count() > 0)
                    <form method="GET" action="{{ route('games.index') }}">
                        <label for="season_select" class="sr-only">Season</label>
                        <select id="season_select" name="season" onchange="this.form.submit()" class="bg-slate-800 border border-slate-700 text-slate-300 rounded px-3 py-2">
                            @foreach($seasons as $season)
                                <option value="{{ $season->id }}" {{ (isset($currentSeason) && $currentSeason->id == $season->id) ? 'selected' : '' }}>Season {{ $season->season_number }}</option>
                            @endforeach
                        </select>
                    </form>
                @elseif(isset($currentSeason))
                    <div class="text-sm text-slate-300">Season {{ $currentSeason->season_number }}</div>
                @endif
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-lg overflow-visible">
            <table class="min-w-full divide-y divide-slate-700">
                <thead class="bg-slate-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm text-slate-400">Date & Time</th>
                        <th class="px-6 py-3 text-left text-sm text-slate-400">Title</th>
                        <th class="px-6 py-3 text-left text-sm text-slate-400">Location</th>
                        <th class="px-6 py-3 text-left text-sm text-slate-400">Price</th>
                        <th class="px-6 py-3 text-left text-sm text-slate-400">Score</th>
                        <th class="px-6 py-3 text-right text-sm text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($games as $game)
                        <tr class="bg-transparent hover:bg-slate-800">
                            <td class="px-6 py-4 text-sm text-slate-300">{{ $game->time->format('M d, Y g:i A') }}</td>
                            <td class="px-6 py-4 text-sm text-ice">{{ $game->title }}</td>
                            <td class="px-6 py-4 text-sm text-slate-300">{{ $game->location }}</td>
                            <td class="px-6 py-4 text-sm text-slate-300">${{ number_format($game->price, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-300">{{ $game->dark_score }} - {{ $game->light_score }}</td>
                            <td class="px-6 py-4 text-right text-sm">
                                <div x-data="{open:false}" class="relative inline-block">
                                    <button @click="open = !open" class="inline-flex items-center gap-2 px-3 py-1 rounded bg-slate-700 text-slate-200 hover:bg-slate-600">
                                        Actions
                                        <svg class="h-4 w-4 text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-40 bg-slate-800 border border-slate-700 rounded shadow-lg z-50">
                                        <a href="{{ route('game_detail.game_id', ['game' => $game->id]) }}" class="block px-3 py-2 text-sm text-ice hover:text-ice hover:bg-slate-700">Details</a>
                                        <a href="{{ route('edit_game', ['game' => $game->id]) }}" class="block px-3 py-2 text-sm text-amber-300 hover:text-amber-300 hover:bg-slate-700">Edit</a>
                                        <a href="{{ route('delete_game', ['game' => $game->id]) }}" onclick="return confirm('Are you sure you want to delete this game?');" class="block px-3 py-2 text-sm text-rose-400 hover:text-rose-400 hover:bg-slate-700">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-300">No games scheduled for this season yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
