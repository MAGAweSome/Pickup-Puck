@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold">Games</h1>
            @if(isset($currentSeason))
                <div class="text-sm text-slate-300">Season {{ $currentSeason->season_number }}</div>
            @endif
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-lg overflow-hidden">
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
                                <a href="{{ route('game_detail.game_id', ['game' => $game->id]) }}" class="px-3 py-1 rounded bg-ice-blue text-deep-navy font-medium">See Details</a>
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
