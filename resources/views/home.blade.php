@extends('layouts.app')

@section('content')

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-ice">Welcome {{{ Auth::user()->name }}}</h1>
                <p class="text-sm text-slate-300">Upcoming and recent games at a glance.</p>
            </div>
            <div class="flex gap-3">
                @role ('admin')
                    <a href="/admin/create_game" class="px-4 py-2 bg-ice-blue text-deep-navy rounded font-medium">Create Game</a>
                @endrole
                @if ($hasNotSignedUpForAllGames && isset($games) && $games->count())
                    <a href="{{ route('seasons.accept-all', ['season' => $games->first()->season_id]) }}" class="px-4 py-2 border border-slate-600 text-ice rounded">Accept All</a>
                @endif
            </div>
        </div>

        <section>
            <h3 class="text-lg font-semibold text-ice mb-3">Upcoming Games</h3>

            @php $upcomingGamesExist = false; @endphp

            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($games as $game)
                    @if ($game->time > $currentTime)
                        @php $upcomingGamesExist = true; @endphp

                        <article class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="text-xl text-ice font-semibold">{{$game->title}}</h4>
                                    <p class="text-sm text-slate-300">{{$game->game_time}} • {{$game->location}}</p>
                                </div>
                                <div class="text-right space-y-1">
                                    @if(in_array($game->id, $gamesAttending))
                                        @include('components.badge', ['status' => 'Attending'])
                                    @else
                                        @include('components.badge', ['status' => 'Not Yet Attending'])
                                    @endif
                                    <div class="text-sm text-slate-300">${{$game->price}}</div>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-sm text-slate-300">{{$game->players->count()}} Players • {{$game->goalies->count()}} Goalies</div>
                                <a href="/game/{{$game->id}}" class="px-3 py-1 bg-ice-blue text-deep-navy rounded">See details</a>
                            </div>
                        </article>

                    @endif
                @endforeach
            </div>

            @if (!$upcomingGamesExist)
                <div class="mt-4 p-4 rounded bg-slate-700 text-slate-200">There Are No Upcoming Games... Yet!</div>
            @endif
        </section>

        <section>
            <h3 class="text-lg font-semibold text-ice mb-3">Previous Games</h3>

            @php $passedGamesExist = false; @endphp

            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($games as $game)
                    @if ($game->time < $currentTime)
                        @php $passedGamesExist = true; @endphp
                        <article class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-lg text-ice">{{$game->title}}</h4>
                                    <p class="text-sm text-slate-300">{{$game->game_time}} • {{$game->location}}</p>
                                </div>
                                <div class="text-sm text-slate-300">${{$game->price}}</div>
                            </div>
                        </article>
                    @endif
                @endforeach
            </div>

            @if (!$passedGamesExist)
                <div class="mt-4 p-4 rounded bg-slate-700 text-slate-200">There Are No Previous Games!</div>
            @endif
        </section>
    </div>

@endsection

@push('scripts')
<script>
    // If URL contains ?startTour=1 then start intro.js tour
    (function(){
        const params = new URLSearchParams(window.location.search);
        if (params.get('startTour') === '1') {
            if (typeof introJs !== 'undefined') {
                try {
                    introJs().start();
                } catch(e) {
                    console.warn('intro.js start failed', e);
                }
            }
            // remove param so refreshing doesn't restart
            params.delete('startTour');
            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.history.replaceState({}, document.title, newUrl);
        }
    })();
</script>
@endpush
