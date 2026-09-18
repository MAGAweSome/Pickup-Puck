@extends('layouts.app')

@section('content')

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-slate-100">{{ $user->name }}'s Game History</h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-400">Past attendance, position records, and payment details.</p>
            </div>
            <a href="{{ route('user_list') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition no-underline shadow-sm self-start sm:self-auto">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>All Players</span>
            </a>
        </div>

        @if($games->isEmpty())
            <div class="p-8 text-center rounded-2xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 text-slate-500 dark:text-slate-400 shadow-sm">
                <i class="fa-regular fa-calendar-xmark text-2xl text-slate-400 dark:text-slate-500 mb-2 block"></i>
                <p class="font-bold text-sm text-slate-800 dark:text-slate-200">No games recorded for this player yet.</p>
            </div>
        @else
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

                    <div class="bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 rounded-2xl p-5 shadow-sm dark:shadow-md flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 group hover:border-sky-400 dark:hover:border-sky-500/50 transition">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <a href="{{ route('game_detail.game_id', ['game' => $game->id]) }}" class="text-base font-bold text-slate-900 dark:text-slate-100 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition block no-underline truncate">{{ $game->title }}</a>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5 font-medium">
                                        <i class="fa-regular fa-calendar text-sky-500 text-[11px]"></i>
                                        <span>{{ $game->gameTime ?? $game->time->isoFormat('ddd, MMM D @ h:mma') }}</span>
                                    </div>
                                </div>
                                <div class="text-xs text-right font-mono text-slate-600 dark:text-slate-400 shrink-0">
                                    <div><span class="text-slate-400 dark:text-slate-500">Price:</span> <strong class="text-slate-900 dark:text-slate-100">${{ $game->price }}</strong></div>
                                    <div class="mt-0.5"><span class="text-slate-400 dark:text-slate-500">Season:</span> {{ $game->season?->season_number ?? '—' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full lg:w-72 flex flex-col gap-2 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100 dark:border-slate-800 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 dark:text-slate-400 font-mono uppercase text-[11px]">Attendance</span>
                                <div>
                                    @if($isFuture)
                                        @if($isAttending)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 font-bold text-[11px]">Attending</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[11px]">Not Yet</span>
                                        @endif
                                    @else
                                        @if($isAttending)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-sky-100 dark:bg-sky-500/20 text-sky-800 dark:text-sky-300 font-bold text-[11px]">Attended</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 font-bold text-[11px]">Did Not Attend</span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            @unless($isFuture && !$isAttending)
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 dark:text-slate-400 font-mono uppercase text-[11px]">Position</span>
                                    <div>
                                        @if(in_array($user->name, $players))
                                            <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium text-[11px]">Player</span>
                                        @elseif(in_array($user->name, $goalies))
                                            <span class="px-2 py-0.5 rounded-lg bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 font-bold text-[11px]">Goalie</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-[11px]">None</span>
                                        @endif
                                    </div>
                                </div>
                            @endunless

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 dark:text-slate-400 font-mono uppercase text-[11px]">Payment</span>
                                <div class="font-mono text-slate-800 dark:text-slate-200 text-[11px]">{{ $payment ? '$'.$payment.' via '.($method ?? '-') : ($isFuture ? 'Not Paid' : '—') }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

@endsection
