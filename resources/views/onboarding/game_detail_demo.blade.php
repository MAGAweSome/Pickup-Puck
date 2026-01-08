@extends('layouts.app')

@section('content')
    @php
        $isOnboarding = request()->boolean('onboarding');
    @endphp

    <div class="max-w-5xl mx-auto px-4 py-6">
        @if($isOnboarding)
            <div id="onboardingBanner" class="mb-6 rounded-lg border border-ice-blue/30 bg-slate-800 p-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-semibold text-ice">Onboarding: Game Details (Example)</h1>
                        <p class="mt-1 text-sm text-slate-300">This page uses example content so the walkthrough works even if your database has no games yet.</p>
                    </div>
                    <a href="{{ route('home') }}" class="px-3 py-1 rounded border border-slate-600 text-ice">Exit</a>
                </div>
            </div>
        @endif

        <div id="gameDetailTable" class="bg-slate-800 border border-slate-700 rounded-lg p-4 mb-4">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-ice">Example Pickup Game</h2>
                    <p class="mt-1 text-slate-300">Friendly skate with rotating teams.</p>
                    <div class="mt-3 flex flex-wrap gap-2 text-sm">
                        <div class="text-slate-300"><span class="text-ice">Fri 9:30 PM</span></div>
                        <div class="text-slate-300">• <span class="text-ice">60 min</span></div>
                        <div class="text-slate-300">• <a class="text-ice-blue" href="https://maps.google.com/?q={{ urlencode('123 Example Arena, Toronto, ON') }}" target="_blank">123 Example Arena, Toronto</a></div>
                        <div class="text-slate-300">• <span class="text-ice">$20</span></div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1 rounded bg-slate-700 text-slate-200">Example</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-4">
                <div class="rounded-lg overflow-hidden border border-slate-700 bg-slate-900">
                    <div class="w-full flex flex-col">
                        <iframe id="gameMap" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q={{ urlencode('123 Example Arena, Toronto, ON') }}&amp;z=14&amp;output=embed" class="w-full" style="min-height: 360px" frameborder="0" marginheight="0" marginwidth="0" loading="lazy"></iframe>
                        <div class="p-3 flex items-center justify-between">
                            <div class="text-sm text-slate-300">Map location (example)</div>
                            <a href="https://maps.google.com/?q={{ urlencode('123 Example Arena, Toronto, ON') }}" target="_blank" class="text-ice-blue text-sm">Open in Maps</a>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div id="acceptGameDiv" class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-ice mb-2">Accept Game</h3>
                        <p class="text-sm text-slate-300 mb-3">In a real game, you choose your position and accept to get on the roster.</p>
                        <div class="flex gap-2">
                            <select disabled class="flex-1 bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice opacity-70">
                                <option selected>Player (default)</option>
                            </select>
                            <button disabled class="px-4 py-2 bg-ice-blue text-deep-navy rounded opacity-60" type="button">Accept</button>
                        </div>
                    </div>

                    <div id="attendingGuestsDiv" class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-ice mb-2">Bring a Guest</h3>
                        <p class="text-sm text-slate-300 mb-3">Add a friend to the roster (example).</p>
                        <div class="space-y-2">
                            <input disabled class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice opacity-70" placeholder="Guest full name" />
                            <select disabled class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice opacity-70">
                                <option selected>Player</option>
                            </select>
                            <button disabled class="w-full px-4 py-2 bg-ice-blue text-deep-navy rounded opacity-60" type="button">Add</button>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="space-y-3 lg:col-span-1">
                <div class="bg-slate-800 border border-slate-700 rounded-lg p-3">
                    <h4 class="text-sm text-slate-300">Quick Info</h4>
                    <div class="mt-2 text-ice text-sm space-y-2">
                        <div class="flex justify-between"><span class="text-slate-300">Players</span><span>10</span></div>
                        <div class="flex justify-between"><span class="text-slate-300">Goalies</span><span>2</span></div>
                        <div class="flex justify-between"><span class="text-slate-300">Price</span><span>$20</span></div>
                        <div class="flex justify-between"><span class="text-slate-300">Season</span><span>—</span></div>
                    </div>
                </div>
            </aside>

            <div class="lg:col-span-3 space-y-4">
                <div id="gameSkaters" class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-ice mb-3">Roster</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-sm text-slate-300 mb-2">Goalies</h4>
                            <ul class="space-y-2">
                                <li class="bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">Alex Goalie</li>
                                <li class="bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">Sam Goalie</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-sm text-slate-300 mb-2">Players</h4>
                            <ul class="space-y-2">
                                <li class="bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">Taylor Skater</li>
                                <li class="bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">Jordan Skater</li>
                                <li class="bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">Casey Skater</li>
                                <li class="bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">Riley Skater</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div id="gameTeam" class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-ice mb-3">Teams (Example)</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="bg-slate-900 border border-slate-700 rounded p-3">
                            <div class="text-sm text-slate-300">Dark</div>
                            <ul class="mt-2 space-y-1 text-ice">
                                <li>Jordan</li>
                                <li>Casey</li>
                                <li>Alex (G)</li>
                            </ul>
                        </div>
                        <div class="bg-slate-900 border border-slate-700 rounded p-3">
                            <div class="text-sm text-slate-300">Light</div>
                            <ul class="mt-2 space-y-1 text-ice">
                                <li>Taylor</li>
                                <li>Riley</li>
                                <li>Sam (G)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button id="onbFinish" type="button" class="px-4 py-2 bg-ice-blue text-deep-navy rounded font-medium">Finish</button>
                </div>
            </div>
        </div>
    </div>

    @if($isOnboarding)
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const btn = document.getElementById('onbFinish');
                    if (!btn) return;

                    btn.addEventListener('click', async () => {
                        btn.disabled = true;

                        try {
                            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            const res = await fetch('{{ route('onboarding.complete') }}', {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrf,
                                }
                            });

                            if (!res.ok) {
                                btn.disabled = false;
                                alert('Unable to complete onboarding. Please try again.');
                                return;
                            }

                            window.location.href = '{{ route('home') }}';
                        } catch (e) {
                            btn.disabled = false;
                            alert('Network error. Please try again.');
                        }
                    });
                });
            </script>
        @endpush
    @endif
@endsection
