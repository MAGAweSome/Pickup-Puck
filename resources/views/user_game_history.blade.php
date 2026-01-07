@extends('layouts.app')

@section('content')

    <div class="max-w-4xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-semibold text-ice mb-6">{{ $user->name }}'s Game History</h1>

        <div class="space-y-4">
            @foreach($games as $game)

                @php
                    $players = $game->players->pluck('name')->toArray();
                    $goalies = $game->goalies->pluck('name')->toArray();
                    $isAttending = in_array($user->name, $players) || in_array($user->name, $goalies);
                    $isFuture = $game->time->isFuture();
                    $payment = $game->gamePayments()->wherePivot('user_id', $user->id)->pluck('payment')->first();
                    $method = $game->gamePayments()->wherePivot('user_id', $user->id)->pluck('method')->first();
                @endphp

                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-start gap-3">
                            <div class="flex-1">
                                <a href="{{ route('game_detail.game_id', ['game' => $game->id]) }}" class="text-lg font-semibold text-ice">{{ $game->title }}</a>
                                <div class="text-slate-300 text-sm">{{ $game->gameTime ?? $game->time->isoFormat('ddd, MMM D @ h:mma') }}</div>
                            </div>
                            <div class="text-sm text-slate-300">
                                <div><span class="font-medium">Price:</span> ${{ $game->price }}</div>
                                <div class="mt-1"><span class="font-medium">Season:</span> {{ $game->season?->season_number ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full lg:w-64 flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-slate-300">Attendance</div>
                            <div>
                                @if($isFuture)
                                    @if($isAttending)
                                        <span class="inline-flex items-center px-2 py-1 rounded bg-emerald-600 text-white text-sm">Attending</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded bg-slate-700 text-slate-300 text-sm">Not Yet</span>
                                    @endif
                                @else
                                    @if($isAttending)
                                        <span class="inline-flex items-center px-2 py-1 rounded bg-ice-blue text-deep-navy text-sm">Attended</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded bg-rose-600 text-white text-sm">Did Not Attend</span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        @unless($isFuture && !$isAttending)
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-slate-300">Position</div>
                                <div class="text-sm">
                                    @if(in_array($user->name, $players))
                                        <span class="px-2 py-1 rounded bg-slate-700 text-slate-200">Player</span>
                                    @elseif(in_array($user->name, $goalies))
                                        <span class="px-2 py-1 rounded bg-slate-700 text-slate-200">Goalie</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-slate-700 text-slate-300">Not Attending</span>
                                    @endif
                                </div>
                            </div>
                        @endunless

                        <div class="flex items-center justify-between">
                            <div class="text-sm text-slate-300">Payment</div>
                            <div class="text-sm text-slate-200">{{ $payment ? '$'.$payment.' via '.($method ?? '-') : ($isFuture ? 'Not Paid' : '—') }}</div>
                        </div>
                    </div>
                </div>

            @endforeach
        </div>

    </div>

@endsection
