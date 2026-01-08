@extends('layouts.app')

@section('content')

    @php
        $isOnboarding = request()->boolean('onboarding');

        $upcomingGames = isset($games)
            ? $games->filter(fn($g) => $g->time > $currentTime)->values()
            : collect();
        $pastGames = isset($games)
            ? $games->filter(fn($g) => $g->time < $currentTime)->values()
            : collect();

        $isSingleUpcoming = $upcomingGames->count() === 1;
        $isSinglePast = $pastGames->count() === 1;
    @endphp

    <div class="space-y-6 max-w-6xl mx-auto w-full">
        <div class="space-y-3 text-center">
            <div>
                <h1 class="text-2xl font-semibold text-ice">Welcome {{{ Auth::user()->name }}}</h1>
                <p class="text-sm text-slate-300">Upcoming and recent games at a glance.</p>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-3">
                @role ('admin')
                    <a href="/admin/create_game" class="px-4 py-2 bg-ice-blue text-deep-navy hover:text-deep-navy rounded font-medium">Create Game</a>
                @endrole
                @if ($hasNotSignedUpForAllGames && isset($games) && $games->count())
                    <a href="{{ route('seasons.accept-all', ['season' => $games->first()->season_id]) }}" class="px-4 py-2 border border-slate-600 text-ice hover:text-ice rounded">Accept All</a>
                @endif
            </div>
        </div>

        <section>
            <div class="{{ $isSingleUpcoming ? 'md:max-w-xl md:mx-auto' : '' }}">
                <h3 class="text-lg font-semibold text-ice mb-3 pl-4">Upcoming Games</h3>

                @if($isOnboarding)
                    <article id="gameCard" class="bg-slate-800 border border-ice-blue/30 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="text-xl text-ice font-semibold">Example Pickup Game</h4>
                                <p class="text-sm text-slate-300">Fri 9:30 PM • 123 Example Arena</p>
                            </div>
                            <div class="text-right space-y-1 text-center">
                                @include('components.badge', ['status' => 'Not Yet Attending'])
                                <div class="text-sm text-slate-300">$20</div>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <div id="gameLocation_Players" class="text-sm text-slate-300">123 Example Arena • 10 Players • 2 Goalies</div>
                            <a id="gameMoreDetails" href="{{ route('games.index', ['onboarding' => 1]) }}" class="px-3 py-1 bg-ice-blue text-deep-navy hover:text-deep-navy rounded">See details</a>
                        </div>
                    </article>
                @endif

                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($upcomingGames as $game)
                        <article class="bg-slate-800 border border-slate-700 rounded-lg p-4 w-full {{ $isSingleUpcoming ? 'md:col-span-2' : '' }}">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="text-xl text-ice font-semibold">{{$game->title}}</h4>
                                    <p class="text-sm text-slate-300">{{$game->game_time}} • {{$game->location}}</p>
                                </div>
                                <div class="text-right space-y-1 text-center">
                                    @if(in_array($game->id, $gamesAttending))
                                        @include('components.badge', ['status' => 'Attending'])
                                    @else
                                        @include('components.badge', ['status' => 'Not Yet Attending'])
                                    @endif
                                    @php
                                        $showPrice = auth()->check()
                                            && auth()->user()->role_preference !== \App\Enums\Games\GameRoles::Goalie->value;
                                    @endphp
                                    @if($showPrice)
                                        <div class="text-sm text-slate-300">${{ $game->price }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-sm text-slate-300">{{$game->players->count()}} Players • {{$game->goalies->count()}} Goalies</div>
                                <a href="/game/{{$game->id}}" class="px-3 py-1 bg-ice-blue text-deep-navy hover:text-deep-navy rounded">See details</a>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($upcomingGames->isEmpty())
                    <div class="mt-4 p-4 rounded bg-slate-700 text-slate-200">There Are No Upcoming Games... Yet!</div>
                @endif
            </div>
        </section>

        <section>
            <div class="{{ $isSinglePast ? 'md:max-w-xl md:mx-auto' : '' }}">
                <h3 class="text-lg font-semibold text-ice mb-3 pl-4">Previous Games</h3>

                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($pastGames as $game)
                        <article class="bg-slate-800 border border-slate-700 rounded-lg p-4 hover:ring-1 hover:ring-slate-600 transition-shadow duration-150 w-full {{ $isSinglePast ? 'md:col-span-2' : '' }}">
                            <div class="flex items-start justify-between gap-3">
                                <h4 class="min-w-0 flex-1 text-xl text-ice font-semibold leading-tight break-words">{{$game->title}}</h4>

                                <div class="flex-shrink-0">
                                    @role('admin')
                                    <button type="button" class="inline-flex items-center rounded-full bg-slate-700/40 ring-1 ring-slate-600 px-2.5 py-1 gap-3 sm:px-4 sm:py-2 sm:gap-6 score-pill" data-game-id="{{$game->id}}">
                                    @else
                                    <div class="inline-flex items-center rounded-full bg-slate-700/40 ring-1 ring-slate-600 px-2.5 py-1 gap-3 sm:px-4 sm:py-2 sm:gap-6">
                                    @endrole
                                        <div class="flex items-baseline gap-1 sm:flex-col sm:items-center sm:gap-0">
                                            <span class="text-xs text-slate-300 sm:underline">Dark:</span>
                                            <span class="text-base font-semibold text-ice sm:text-lg" data-score-part="dark">{{$game->dark_score}}</span>
                                        </div>

                                        <div class="w-px h-4 bg-slate-600/50 sm:h-8" aria-hidden="true"></div>

                                        <div class="flex items-baseline gap-1 sm:flex-col sm:items-center sm:gap-0">
                                            <span class="text-xs text-slate-300 sm:underline">Light:</span>
                                            <span class="text-base font-semibold text-ice sm:text-lg" data-score-part="light">{{$game->light_score}}</span>
                                        </div>
                                    @role('admin')
                                    </button>
                                    @else
                                    </div>
                                    @endrole
                                </div>
                            </div>

                            <div class="mt-3 flex flex-col gap-2 text-sm text-slate-300 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <div class="break-words">{{$game->game_time}}</div>
                                    <div class="mt-1 text-xs text-slate-300 sm:hidden">{{$game->players->count()}} Players • {{$game->goalies->count()}} Goalies</div>
                                </div>

                                <div class="hidden sm:flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-700 text-slate-300 text-xs">{{$game->players->count()}} Players</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-700 text-slate-300 text-xs">{{$game->goalies->count()}} Goalies</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($pastGames->isEmpty())
                    <div class="mt-4 p-4 rounded bg-slate-700 text-slate-200">There Are No Previous Games!</div>
                @endif
            </div>
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
<script>
    (() => {
        let csrf = null;

            function createModalHtml() {
            return `
            <div id="score-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
                    <div class="absolute inset-0 bg-black/50 modal-backdrop z-40"></div>
                <div class="relative z-50 bg-slate-800 rounded-lg p-6 w-full max-w-md">
                    <h3 class="text-lg text-ice mb-3">Edit Score</h3>
                    <form id="score-form" class="space-y-3">
                        <input type="hidden" name="game_id" id="modal-game-id" />
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-slate-300">Dark</label>
                                <input id="modal-dark-score" name="dark_score" type="number" min="0" class="w-full mt-1 p-2 rounded bg-slate-700 text-ice" />
                            </div>
                            <div>
                                <label class="text-xs text-slate-300">Light</label>
                                <input id="modal-light-score" name="light_score" type="number" min="0" class="w-full mt-1 p-2 rounded bg-slate-700 text-ice" />
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" id="score-cancel" class="px-3 py-1 bg-slate-700 rounded">Cancel</button>
                            <button type="submit" id="score-save" class="px-3 py-1 bg-ice-blue text-deep-navy rounded">Save</button>
                        </div>
                        <div id="score-error" class="text-red-400 text-sm mt-2 hidden"></div>
                    </form>
                </div>
            </div>
            `;
        }

        document.addEventListener('DOMContentLoaded', () => {
            csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            // insert modal into body
            const wrapper = document.createElement('div');
            wrapper.innerHTML = createModalHtml();
            document.body.appendChild(wrapper.firstElementChild);

            const modal = document.getElementById('score-modal');
            const form = document.getElementById('score-form');
            const darkInput = document.getElementById('modal-dark-score');
            const lightInput = document.getElementById('modal-light-score');
            const gameIdInput = document.getElementById('modal-game-id');
            const errorEl = document.getElementById('score-error');

            function openModal(gameId, dark, light) {
                gameIdInput.value = gameId;
                darkInput.value = dark;
                lightInput.value = light;
                errorEl.classList.add('hidden');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            document.body.addEventListener('click', (e) => {
                const btn = e.target.closest('.score-pill');
                if (btn) {
                    e.preventDefault();
                    const gameId = btn.getAttribute('data-game-id');
                    // find current scores from the pill
                    const dark = btn.querySelector('[data-score-part="dark"]')?.textContent.trim() || '0';
                    const light = btn.querySelector('[data-score-part="light"]')?.textContent.trim() || '0';
                    openModal(gameId, dark, light);
                }
            });

            // close modal when clicking outside content (backdrop)
            modal.addEventListener('click', (e) => {
                if (e.target.classList.contains('modal-backdrop') || e.target.id === 'score-modal') {
                    closeModal();
                }
            });

            document.getElementById('score-cancel').addEventListener('click', (e) => {
                e.preventDefault();
                closeModal();
            });

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const gameId = gameIdInput.value;
                const dark = parseInt(darkInput.value || 0, 10);
                const light = parseInt(lightInput.value || 0, 10);
                errorEl.classList.add('hidden');

                try {
                    const res = await fetch(`/admin/game/${gameId}/score`, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrf || ''
                        },
                        body: JSON.stringify({ dark_score: dark, light_score: light })
                    });

                    let data = {};
                    try { data = await res.json(); } catch(e){ /* ignore parse errors */ }

                    if (!res.ok) {
                        if (res.status === 419) {
                            errorEl.textContent = 'Session expired or invalid CSRF token. Please refresh and try again.';
                        } else {
                            errorEl.textContent = data.error || (`Unable to save scores (status ${res.status})`);
                        }
                        errorEl.classList.remove('hidden');
                        return;
                    }

                    // update pill UI with new values
                    const pill = document.querySelector(`.score-pill[data-game-id="${gameId}"]`);
                    if (pill) {
                        const darkEl = pill.querySelector('[data-score-part="dark"]');
                        const lightEl = pill.querySelector('[data-score-part="light"]');
                        if (darkEl) darkEl.textContent = data.dark_score;
                        if (lightEl) lightEl.textContent = data.light_score;
                    }

                    closeModal();
                } catch (err) {
                    errorEl.textContent = 'Network error';
                    errorEl.classList.remove('hidden');
                }
            });
        });
    })();
</script>
@endpush
