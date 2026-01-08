@extends('layouts.app')

@section('content')

    <div class="max-w-4xl mx-auto px-4 py-6">
        <div class="flex items-start justify-between gap-4">
            <h1 class="text-2xl font-semibold text-ice mb-6">{{ $guest->name }}'s Game History</h1>
            <a href="{{ route('user_list') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-800/60 ring-1 ring-white/10 px-3 py-2 text-sm font-semibold text-ice hover:text-ice hover:bg-slate-700/60 transition">All Players</a>
        </div>

        @if(($games ?? collect())->isEmpty())
            <div class="mt-4 p-4 rounded bg-slate-700 text-slate-200">No games found for this guest.</div>
        @else
            <div class="space-y-4">
                @foreach($games as $game)
                    @php
                        $isFuture = $game->time->isFuture();
                        $role = $roleByGameId[$game->id] ?? null;
                    @endphp

                    <div class="bg-slate-800 border border-slate-700 rounded-lg p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-start gap-3">
                                <div class="flex-1">
                                    <a href="{{ route('game_detail.game_id', ['game' => $game->id]) }}" class="text-lg font-semibold text-ice">{{ $game->title }}</a>
                                    <div class="text-slate-300 text-sm">{{ $game->gameTime ?? $game->time->isoFormat('ddd, MMM D @ h:mma') }}</div>
                                </div>
                                <div class="text-sm text-slate-300">
                                    <div><span class="font-medium">Score:</span> {{ $game->dark_score }} - {{ $game->light_score }}</div>
                                    <div class="mt-1"><span class="font-medium">Season:</span> {{ $game->season?->season_number ?? '—' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full lg:w-64 flex flex-col gap-2">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-slate-300">Attendance</div>
                                <div>
                                    @if($isFuture)
                                        <span class="inline-flex items-center px-2 py-1 rounded bg-emerald-600 text-white text-sm">Attending</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded bg-ice-blue text-deep-navy text-sm">Attended</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="text-sm text-slate-300">Position</div>
                                <div class="text-sm">
                                    @if($role)
                                        <span class="px-2 py-1 rounded bg-slate-700 text-slate-200">{{ \Illuminate\Support\Str::title($role) }}</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-slate-700 text-slate-300">—</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="text-sm text-slate-300">Payment</div>
                                <div class="text-sm text-slate-200">—</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

@endsection
