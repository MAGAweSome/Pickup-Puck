@extends('layouts.app')

@section('content')
<div class="space-y-12 sm:space-y-16 w-full">

    <!-- Hero Showcase Section -->
    <section class="relative overflow-hidden rounded-3xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 p-8 sm:p-12 lg:p-16 shadow-sm dark:shadow-md transition-colors">
        <!-- Radial Lighting Accents -->
        <div class="absolute -right-24 -bottom-24 w-96 h-96 bg-sky-500/10 dark:bg-sky-500/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-24 -top-24 w-96 h-96 bg-cyan-400/10 dark:bg-cyan-400/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-4xl mx-auto text-center space-y-6">
            <!-- League Beacon Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-mono font-bold bg-sky-50 dark:bg-sky-500/15 border border-sky-200 dark:border-sky-500/30 text-sky-700 dark:text-sky-300 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Recreational Pickup Hockey — Cambridge, Ontario</span>
            </div>

            <!-- Main Headline -->
            <h1 class="text-3xl sm:text-5xl md:text-6xl font-black tracking-tight text-slate-900 dark:text-slate-100 leading-tight">
                Pickup Hockey,<br class="hidden sm:inline">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-600 via-cyan-500 to-sky-500 dark:from-sky-400 dark:via-cyan-300 dark:to-sky-300">
                    Balanced &amp; Perfected.
                </span>
            </h1>

            <!-- Subtitle -->
            <p class="max-w-2xl mx-auto text-sm sm:text-base md:text-lg text-slate-600 dark:text-slate-300 leading-relaxed">
                Same core crew - fresh balanced rosters - love of the game!
            </p>

            <!-- Call-to-Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                <a href="{{ route('login') }}?form=register" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-gradient-to-r from-sky-500 to-cyan-400 hover:from-sky-400 hover:to-cyan-300 text-slate-950 font-black text-sm transition shadow-lg shadow-sky-500/20 flex items-center justify-center gap-2 no-underline">
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    <span>Create Account &amp; Play</span>
                </a>
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold text-sm transition shadow-sm flex items-center justify-center gap-2 no-underline">
                    <i class="fa-solid fa-right-to-bracket text-xs"></i>
                    <span>Sign In to Locker Room</span>
                </a>
            </div>

            <!-- 4-Stat Metric Highlights -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 pt-8 max-w-3xl mx-auto">
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700/60 shadow-sm text-center">
                    <div class="text-2xl sm:text-3xl font-black font-mono text-sky-600 dark:text-sky-400">100%</div>
                    <div class="text-xs text-slate-600 dark:text-slate-400 mt-1 font-semibold">Even Rosters</div>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700/60 shadow-sm text-center">
                    <div class="text-2xl sm:text-3xl font-black font-mono text-slate-900 dark:text-slate-100">16-65+</div>
                    <div class="text-xs text-slate-600 dark:text-slate-400 mt-1 font-semibold">All Ages &amp; Tiers</div>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700/60 shadow-sm text-center">
                    <div class="text-2xl sm:text-3xl font-black font-mono text-cyan-600 dark:text-cyan-400">T-30</div>
                    <div class="text-xs text-slate-600 dark:text-slate-400 mt-1 font-semibold">Live Reveal</div>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700/60 shadow-sm text-center">
                    <div class="text-2xl sm:text-3xl font-black font-mono text-slate-900 dark:text-slate-100">0</div>
                    <div class="text-xs text-slate-600 dark:text-slate-400 mt-1 font-semibold">Stacked Teams</div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="space-y-6">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <span class="text-xs uppercase font-mono font-bold tracking-widest text-sky-600 dark:text-sky-400">Simple Game Day Flow</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">How Our League Operates</h2>
            <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm">
                No endless group chats or guessing who is in. Here is how Pickup Puck makes every game night seamless.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Step 1 -->
            <div class="rounded-3xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 p-6 sm:p-7 shadow-sm dark:shadow-md flex flex-col justify-between transition-colors">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-2xl bg-sky-50 dark:bg-sky-500/15 border border-sky-200 dark:border-sky-500/30 flex items-center justify-center text-sky-600 dark:text-sky-400 font-black text-base shadow-sm">
                        1
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">RSVP With 1 Click</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Confirm whether you are attending or busy. Real-time counters track skaters and goalies live.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2 text-xs font-semibold text-sky-600 dark:text-sky-400">
                    <i class="fa-regular fa-calendar-check"></i>
                    <span>Google &amp; Apple Calendar Sync</span>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="rounded-3xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 p-6 sm:p-7 shadow-sm dark:shadow-md flex flex-col justify-between transition-colors">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-2xl bg-cyan-50 dark:bg-cyan-500/15 border border-cyan-200 dark:border-cyan-500/30 flex items-center justify-center text-cyan-600 dark:text-cyan-400 font-black text-base shadow-sm">
                        2
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">T-30 Roster Reveal</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Exactly 30 minutes before puck drop, the algorithm drafts Dark &amp; Light teams balanced by player skill level, history, and goalie parity.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2 text-xs font-semibold text-cyan-600 dark:text-cyan-400">
                    <i class="fa-solid fa-scale-balanced"></i>
                    <span>Tiered Pairings &amp; Equal Skill</span>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="rounded-3xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 p-6 sm:p-7 shadow-sm dark:shadow-md flex flex-col justify-between transition-colors">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-black text-base shadow-sm">
                        3
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Hit The Ice</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Bring your Dark and Light jerseys, lace up, and play high-tempo, respectful hockey.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                    <i class="fa-solid fa-hockey-puck"></i>
                    <span>Competitive, friendly, no blowouts</span>
                </div>
            </div>
        </div>
    </section>

    <!-- The Atmosphere & League Culture Section -->
    <section id="atmosphere" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
        <div class="lg:col-span-6 space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono font-bold bg-sky-50 dark:bg-sky-500/15 border border-sky-200 dark:border-sky-500/30 text-sky-700 dark:text-sky-300">
                <i class="fa-solid fa-people-group text-xs"></i>
                <span>The Pickup Puck Culture</span>
            </div>

            <h2 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-slate-100 tracking-tight leading-tight">
                Real Hockey with Great People.<br>
                <!-- <span class="text-sky-600 dark:text-sky-400">No Cliques. No Politics.</span> -->
            </h2>

            <div class="space-y-3 text-slate-600 dark:text-slate-300 text-xs sm:text-sm leading-relaxed">
                <p>
                    Unlike traditional beer leagues where static teams breed rivalries or lopsided divisions, Pickup Puck shuffles the rosters every game. One night you’re defending against someone, the next you’re connecting on a tape-to-tape breakout pass.
                </p>
                <p>
                    Whether you played junior or college hockey or picked up skates later in life, everyone gets equal shifts, good puck movement, and respectful play.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 shadow-sm">
                    <i class="fa-solid fa-arrows-rotate text-sky-500 mt-0.5 text-sm"></i>
                    <div>
                        <div class="text-xs font-bold text-slate-900 dark:text-slate-100">Rotating Teammates</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Skate with everyone each season</div>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 shadow-sm">
                    <i class="fa-solid fa-shield-heart text-sky-500 mt-0.5 text-sm"></i>
                    <div>
                        <div class="text-xs font-bold text-slate-900 dark:text-slate-100">Clean &amp; Respectful</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Competitive without the cheap stuff</div>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 shadow-sm">
                    <i class="fa-solid fa-person-skating text-sky-500 mt-0.5 text-sm"></i>
                    <div>
                        <div class="text-xs font-bold text-slate-900 dark:text-slate-100">All Generations</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Teens to 65+ skating side by side</div>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 shadow-sm">
                    <i class="fa-solid fa-stopwatch text-sky-500 mt-0.5 text-sm"></i>
                    <div>
                        <div class="text-xs font-bold text-slate-900 dark:text-slate-100">Equal Ice Time</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Short shifts, fast legs, pure fun</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Visual Mockup Card -->
        <div id="rosters" class="lg:col-span-6 rounded-3xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 p-6 sm:p-7 shadow-sm dark:shadow-md transition-colors space-y-5">
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🏒</span>
                    <span class="font-bold text-slate-900 dark:text-slate-100 text-sm uppercase tracking-wider font-mono">Sample Balanced Matchup</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30">
                    50/50 Balanced
                </span>
            </div>

            <!-- Team Jersey Cards Grid -->
            <div class="grid grid-cols-2 gap-4 text-xs">
                <!-- Dark Team -->
                <div class="rounded-2xl bg-slate-900 dark:bg-[#0f172a] border border-slate-800 text-white p-4 text-center shadow-sm">
                    <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-600 text-white font-mono font-black text-xs flex items-center justify-center mx-auto mb-2 shadow-inner">
                        D
                    </div>
                    <div class="text-sm font-black uppercase font-mono tracking-wider text-slate-100">Dark Team</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Dark Jersey</div>

                    <div class="mt-4 space-y-1.5 text-left border-t border-slate-800 pt-3 text-[11px]">
                        <div class="flex justify-between text-slate-300">
                            <span>Top Tier:</span>
                            <span class="font-mono font-bold text-white">2 Skaters</span>
                        </div>
                        <div class="flex justify-between text-slate-300">
                            <span>Mid Tier:</span>
                            <span class="font-mono font-bold text-white">4 Skaters</span>
                        </div>
                        <div class="flex justify-between text-slate-300">
                            <span>Dev Tier:</span>
                            <span class="font-mono font-bold text-white">2 Skaters</span>
                        </div>
                        <div class="flex justify-between text-slate-300">
                            <span>Goalie:</span>
                            <span class="font-mono font-bold text-sky-400">Level 4</span>
                        </div>
                    </div>
                </div>

                <!-- Light Team -->
                <div class="rounded-2xl bg-slate-50 border-2 border-sky-300 p-4 text-center shadow-sm">
                    <div class="w-8 h-8 rounded-full bg-white border border-sky-400 text-sky-700 font-mono font-black text-xs flex items-center justify-center mx-auto mb-2 shadow-sm">
                        L
                    </div>
                    <div class="text-sm font-black uppercase font-mono tracking-wider text-slate-900">Light Team</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">White Jersey</div>

                    <div class="mt-4 space-y-1.5 text-left border-t border-slate-200 pt-3 text-[11px]">
                        <div class="flex justify-between text-slate-600">
                            <span>Top Tier:</span>
                            <span class="font-mono font-bold text-slate-900">2 Skaters</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Mid Tier:</span>
                            <span class="font-mono font-bold text-slate-900">4 Skaters</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Dev Tier:</span>
                            <span class="font-mono font-bold text-slate-900">2 Skaters</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Goalie:</span>
                            <span class="font-mono font-bold text-sky-600">Level 4</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center text-xs text-slate-500 dark:text-slate-400 pt-1">
                <i class="fa-solid fa-lock text-sky-500 mr-1"></i>
                <span>Rosters lock and reveal precisely 30 minutes before puck drop.</span>
            </div>
        </div>
    </section>

    <!-- Final Call to Action Banner -->
    <section class="rounded-3xl bg-gradient-to-r from-sky-50 via-white to-sky-50 dark:from-[#0f172a] dark:via-[#1e293b] dark:to-[#0f172a] border border-sky-200 dark:border-slate-700/80 p-8 sm:p-12 text-center shadow-sm dark:shadow-md transition-colors">
        <div class="max-w-2xl mx-auto space-y-4">
            <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">
                Ready for Your Next Shift?
            </h3>
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                Sign in to confirm your attendance, check rink locations and ice times, and see your team assignments live.
            </p>
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('login') }}?form=register" class="w-full sm:w-auto px-7 py-3 rounded-xl bg-gradient-to-r from-sky-500 to-cyan-400 hover:from-sky-400 hover:to-cyan-300 text-slate-950 font-black text-sm transition shadow-lg shadow-sky-500/20 flex items-center justify-center gap-2 no-underline">
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    <span>Register New Account</span>
                </a>
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold text-sm transition shadow-sm flex items-center justify-center gap-2 no-underline">
                    <span>Sign In</span>
                </a>
            </div>
        </div>
    </section>

</div>
@endsection
