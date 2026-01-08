@extends('layouts.app')

@section('content')
    @php
        $isAdmin = auth()->check() && auth()->user()->hasRole('admin');
        $showPrice = auth()->check()
            && auth()->user()->role_preference !== \App\Enums\Games\GameRoles::Goalie->value;
    @endphp
    <div class="max-w-6xl mx-auto">
        @php
            $isOnboarding = request()->boolean('onboarding');
        @endphp

        @if($isOnboarding)
            <div class="mb-6 rounded-lg border border-ice-blue/30 bg-slate-800 p-4">
                <h1 class="text-xl font-semibold text-ice">Onboarding: Games</h1>
                <p class="mt-1 text-sm text-slate-300">This list can show demo content so onboarding works even before any games are created.</p>
            </div>
        @endif

        <div class="flex flex-col gap-3 mb-6 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-3xl font-bold">Games</h1>

            <div class="flex items-center gap-4 sm:justify-end">
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

        {{-- Cards (all screen sizes) --}}
        <div class="grid gap-4 md:grid-cols-2">
            @if($isOnboarding && (!isset($games) || $games->isEmpty()))
                <article class="bg-slate-800 border border-ice-blue/30 rounded-lg p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="text-lg text-ice font-semibold">Example Pickup Game</h3>
                            <p class="mt-1 text-sm text-slate-300">Fri, Jan 9 9:30 PM • 123 Example Arena</p>
                        </div>
                        @if($showPrice)
                            <div class="text-sm text-slate-300 flex-shrink-0">$20.00</div>
                        @endif
                    </div>

                    <div class="mt-3 flex items-center justify-between">
                        <div class="text-sm text-slate-300">Score: —</div>
                        <a id="onbGameDetailsLink" href="{{ route('onboarding.game-details', ['onboarding' => 1]) }}" class="inline-flex items-center px-3 py-1 rounded bg-slate-700 text-slate-200 hover:text-slate-200 hover:bg-slate-600 no-underline">Details</a>
                    </div>
                </article>
            @endif

            @forelse($games as $game)
                <article class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="text-lg text-ice font-semibold break-words">{{ $game->title }}</h3>
                            <p class="mt-1 text-sm text-slate-300 break-words">
                                {{ $game->time->format('M d, Y g:i A') }} • {{ $game->location }}
                            </p>
                        </div>
                        @if($showPrice)
                            <div class="text-sm text-slate-300 flex-shrink-0">${{ number_format($game->price, 2) }}</div>
                        @endif
                    </div>

                    <div class="mt-3 flex flex-col gap-3">
                        <div class="text-sm text-slate-300">Score: {{ $game->dark_score }} - {{ $game->light_score }}</div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('game_detail.game_id', ['game' => $game->id]) }}" class="inline-flex items-center px-3 py-1 rounded bg-slate-700 text-slate-200 hover:text-slate-200 hover:bg-slate-600 no-underline">Details</a>
                            @if($isAdmin)
                                <a href="{{ route('edit_game', ['game' => $game->id]) }}" class="inline-flex items-center px-3 py-1 rounded bg-slate-700 text-amber-300 hover:text-amber-300 hover:bg-slate-600 no-underline">Edit</a>
                                <a href="{{ route('delete_game', ['game' => $game->id]) }}" onclick="return confirm('Are you sure you want to delete this game?');" class="inline-flex items-center px-3 py-1 rounded bg-slate-700 text-rose-400 hover:text-rose-400 hover:bg-slate-600 no-underline">Delete</a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="md:col-span-2 p-4 rounded bg-slate-700 text-slate-200">No games scheduled for this season yet.</div>
            @endforelse
        </div>
    </div>
@endsection
