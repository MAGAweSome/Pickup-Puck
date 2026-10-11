@if(!empty($currentUserTeam))
    <div class="mb-5">
        <div class="inline-flex items-center gap-2.5 rounded-2xl bg-emerald-100 dark:bg-emerald-950/70 border border-emerald-300 dark:border-emerald-400/40 px-4 py-2 text-emerald-900 dark:text-emerald-200 shadow-sm dark:shadow-[0_0_20px_rgba(16,185,129,0.2)]">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-600 dark:bg-emerald-400"></span>
            </span>
            <span class="text-xs font-semibold text-emerald-800 dark:text-emerald-300">You are assigned to</span>
            <span class="font-black uppercase tracking-widest text-white bg-emerald-600 px-2.5 py-0.5 rounded-lg text-xs font-mono shadow-sm">{{ $currentUserTeam }} Team</span>
        </div>
    </div>
@endif

@php
    $darkSkatersCount = ($darkTeamMembers ?? collect())->filter(fn ($m) => empty($m['is_goalie']) && empty($m['is_empty_net']))->count();
    $lightSkatersCount = ($lightTeamMembers ?? collect())->filter(fn ($m) => empty($m['is_goalie']) && empty($m['is_empty_net']))->count();
@endphp

<div class="grid md:grid-cols-2 gap-5">
    <!-- Dark Team Jersey Card -->
    <div class="relative overflow-hidden rounded-3xl bg-slate-900/95 dark:bg-[#1e293b] text-white border border-slate-800 dark:border-slate-700/80 shadow-xl p-5 sm:p-6 flex flex-col justify-between transition-all">
        <div>
            <div class="flex items-center justify-between pb-3.5 mb-3.5 border-b border-slate-800/90 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="w-4 h-4 rounded-full bg-slate-800 border-2 border-slate-400 shadow-sm ring-2 ring-slate-950/50"></span>
                    <div>
                        <h4 class="font-black text-base text-white tracking-wider uppercase font-mono">Dark Team</h4>
                        <span class="text-[11px] text-slate-400 font-mono">{{ $darkSkatersCount }} skaters</span>
                    </div>
                </div>
                @role('admin')
                    <div class="text-[11px] font-mono text-slate-300 bg-slate-950/80 px-2.5 py-1 rounded-xl border border-slate-800 flex items-center gap-1.5 shadow-inner">
                        <span class="text-slate-400">Skaters Score:</span>
                        <span class="font-black text-sky-400">{{ $darkTeamSkill ?? 0 }}</span>
                    </div>
                @endrole
            </div>

            <ul class="space-y-2">
                @forelse(($darkTeamMembers ?? collect()) as $m)
                    @php
                        $isGoalie = !empty($m['is_goalie']);
                        $isEmptyNet = !empty($m['is_empty_net']);
                        $isCurrentUser = !empty($m['is_current_user']);
                        $lvl = (int) ($m['level'] ?? 3);
                    @endphp
                    <li class="rounded-2xl px-3.5 py-2.5 border flex items-center justify-between gap-2.5 transition duration-150 @if($isCurrentUser) bg-emerald-950/80 border-emerald-400/80 text-white font-bold ring-1 ring-emerald-400/40 @elseif($isGoalie && !$isEmptyNet) bg-gradient-to-r from-sky-950/70 to-indigo-950/40 border-sky-400/60 dark:border-cyan-400/50 text-sky-200 font-semibold shadow-sm @else bg-slate-900/90 dark:bg-slate-950/70 border-slate-800 hover:border-slate-700 text-slate-200 @endif @if($isEmptyNet) bg-slate-950/40 border-dashed border-slate-800 text-slate-500 italic @endif">
                        <span class="flex items-center gap-2.5 min-w-0">
                            @if($isGoalie && !$isEmptyNet)
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-black uppercase tracking-wider bg-sky-500/20 text-sky-300 border border-sky-400/50 flex items-center gap-1 shrink-0 shadow-sm">
                                    <i class="fa-solid fa-shield-halved text-[9px]"></i>
                                    <span>G</span>
                                </span>
                            @endif
                            <span class="truncate text-sm font-medium">{{ $m['name'] }}</span>
                            @if(auth()->check() && auth()->user()->hasRole('admin') && empty($m['is_empty_net']))
                                @if($lvl <= 1)
                                    <span class="text-[10px] font-mono font-bold rounded-lg px-2 py-0.5 bg-[#38150c] text-[#fba778] border border-[#a04624]/70 shrink-0" title="Tier 1: Bronze">Lvl 1</span>
                                @elseif($lvl == 2)
                                    <span class="text-[10px] font-mono font-bold rounded-lg px-2 py-0.5 bg-slate-800 text-slate-200 border border-slate-500/60 shrink-0" title="Tier 2: Silver">Lvl 2</span>
                                @elseif($lvl == 3)
                                    <span class="text-[10px] font-mono font-bold rounded-lg px-2 py-0.5 bg-yellow-950/60 text-yellow-300 border border-yellow-500/60 shrink-0" title="Tier 3: Gold">Lvl 3</span>
                                @else
                                    <span class="text-[10px] font-mono font-black rounded-lg px-2 py-0.5 bg-cyan-950/70 text-cyan-300 border border-cyan-400/60 shrink-0" title="Tier 4+: Platinum / Diamond">Lvl {{ $lvl }}</span>
                                @endif
                            @endif
                        </span>

                        <span class="flex items-center gap-2 shrink-0">
                            @if($isCurrentUser)
                                <span class="text-[10px] font-mono rounded-full bg-emerald-500/20 border border-emerald-400/40 px-2 py-0.5 text-emerald-300 font-bold">You</span>
                            @endif
                            @if(auth()->check() && auth()->user()->hasRole('admin') && empty($m['is_empty_net']) && !empty($m['type']) && in_array($m['type'], ['user','guest']) && !empty($m['id']))
                                <div class="relative">
                                    <button class="player-options-btn px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition text-xs" title="Player actions">⋮</button>
                                    <div class="player-options-menu hidden absolute right-0 mt-2 w-44 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl z-50 py-1 text-xs">
                                        <button data-member-type="{{ $m['type'] }}" data-member-id="{{ $m['id'] }}" data-target-team="2" class="w-full text-left px-3 py-2 text-slate-200 hover:bg-slate-800 hover:text-white admin-move-team">Move &rarr; Light</button>
                                    </div>
                                </div>
                            @endif
                        </span>
                    </li>
                @empty
                    <li class="text-slate-500 text-xs py-3 text-center">Rosters pending generation.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Light Team Jersey Card -->
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-[#1e293b] text-slate-900 dark:text-white border-2 border-sky-300 dark:border-cyan-500/40 shadow-xl p-5 sm:p-6 flex flex-col justify-between transition-colors">
        <div>
            <div class="flex items-center justify-between pb-3.5 mb-3.5 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="w-4 h-4 rounded-full bg-white border-2 border-sky-500 shadow-sm ring-2 ring-sky-200 dark:ring-sky-900/50"></span>
                    <div>
                        <h4 class="font-black text-base text-slate-900 dark:text-white tracking-wider uppercase font-mono">Light Team</h4>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $lightSkatersCount }} skaters</span>
                    </div>
                </div>
                @role('admin')
                    <div class="text-[11px] font-mono text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-950/80 px-2.5 py-1 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center gap-1.5 shadow-inner">
                        <span class="text-slate-500 dark:text-slate-400">Skaters Score:</span>
                        <span class="font-black text-sky-600 dark:text-cyan-400">{{ $lightTeamSkill ?? 0 }}</span>
                    </div>
                @endrole
            </div>

            <ul class="space-y-2">
                @forelse(($lightTeamMembers ?? collect()) as $m)
                    @php
                        $isGoalie = !empty($m['is_goalie']);
                        $isEmptyNet = !empty($m['is_empty_net']);
                        $isCurrentUser = !empty($m['is_current_user']);
                        $lvl = (int) ($m['level'] ?? 3);
                    @endphp
                    <li class="rounded-2xl px-3.5 py-2.5 border flex items-center justify-between gap-2.5 transition duration-150 @if($isCurrentUser) bg-emerald-50 dark:bg-emerald-950/70 border-emerald-400 text-emerald-900 dark:text-white font-bold ring-1 ring-emerald-400/40 @elseif($isGoalie && !$isEmptyNet) bg-sky-50/90 dark:bg-cyan-950/50 border-sky-400/80 dark:border-cyan-400/60 text-sky-950 dark:text-cyan-200 font-semibold shadow-sm @else bg-slate-50 dark:bg-slate-950/70 border-slate-200 dark:border-slate-800/90 hover:border-sky-400 text-slate-800 dark:text-slate-200 @endif @if($isEmptyNet) bg-slate-100/50 dark:bg-slate-950/40 border-dashed border-slate-300 dark:border-slate-800 text-slate-400 italic @endif">
                        <span class="flex items-center gap-2.5 min-w-0">
                            @if($isGoalie && !$isEmptyNet)
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-black uppercase tracking-wider bg-sky-500 text-white dark:bg-cyan-500/25 dark:text-cyan-300 border border-sky-400 dark:border-cyan-400/50 flex items-center gap-1 shrink-0 shadow-sm">
                                    <i class="fa-solid fa-shield-halved text-[9px]"></i>
                                    <span>G</span>
                                </span>
                            @endif
                            <span class="truncate text-sm font-medium">{{ $m['name'] }}</span>
                            @if(auth()->check() && auth()->user()->hasRole('admin') && empty($m['is_empty_net']))
                                @if($lvl <= 1)
                                    <span class="text-[10px] font-mono font-bold rounded-lg px-2 py-0.5 bg-[#4a1c10]/15 dark:bg-[#38150c] text-[#9a3412] dark:text-[#fba778] border border-[#a04624]/40 dark:border-[#a04624]/70 shrink-0" title="Tier 1: Bronze">Lvl 1</span>
                                @elseif($lvl == 2)
                                    <span class="text-[10px] font-mono font-bold rounded-lg px-2 py-0.5 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-600/60 shrink-0" title="Tier 2: Silver">Lvl 2</span>
                                @elseif($lvl == 3)
                                    <span class="text-[10px] font-mono font-bold rounded-lg px-2 py-0.5 bg-yellow-100 dark:bg-yellow-950/60 text-yellow-900 dark:text-yellow-300 border border-yellow-300 dark:border-yellow-500/60 shrink-0" title="Tier 3: Gold">Lvl 3</span>
                                @else
                                    <span class="text-[10px] font-mono font-black rounded-lg px-2 py-0.5 bg-cyan-100 dark:bg-cyan-950/70 text-cyan-900 dark:text-cyan-300 border border-cyan-300 dark:border-cyan-400/60 shrink-0" title="Tier 4+: Platinum / Diamond">Lvl {{ $lvl }}</span>
                                @endif
                            @endif
                        </span>

                        <span class="flex items-center gap-2 shrink-0">
                            @if($isCurrentUser)
                                <span class="text-[10px] font-mono rounded-full bg-emerald-100 dark:bg-emerald-500/20 border border-emerald-300 dark:border-emerald-400/40 px-2 py-0.5 text-emerald-800 dark:text-emerald-300 font-bold">You</span>
                            @endif
                            @if(auth()->check() && auth()->user()->hasRole('admin') && empty($m['is_empty_net']) && !empty($m['type']) && in_array($m['type'], ['user','guest']) && !empty($m['id']))
                                <div class="relative">
                                    <button class="player-options-btn px-2 py-1 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition text-xs" title="Player actions">⋮</button>
                                    <div class="player-options-menu hidden absolute right-0 mt-2 w-44 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl z-50 py-1 text-xs">
                                        <button data-member-type="{{ $m['type'] }}" data-member-id="{{ $m['id'] }}" data-target-team="1" class="w-full text-left px-3 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 admin-move-team">Move &rarr; Dark</button>
                                    </div>
                                </div>
                            @endif
                        </span>
                    </li>
                @empty
                    <li class="text-slate-400 text-xs py-3 text-center">Rosters pending generation.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
