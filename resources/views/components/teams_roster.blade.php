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

<div class="grid md:grid-cols-2 gap-5">
    <!-- Dark Team Jersey Card -->
    <div class="relative overflow-hidden rounded-3xl bg-slate-900 dark:bg-[#1e293b] text-white border border-slate-800 dark:border-slate-700/80 shadow-lg p-5 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between pb-3.5 mb-3.5 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="w-3.5 h-3.5 rounded-full bg-slate-700 border-2 border-slate-400 shadow-sm"></span>
                    <div>
                        <h4 class="font-black text-base text-white tracking-wider uppercase font-mono">Dark Team</h4>
                        <span class="text-[11px] text-slate-400 font-mono">{{ count($darkTeamMembers ?? []) }} skaters</span>
                    </div>
                </div>
                @role('admin')
                    <div class="text-[11px] font-mono text-slate-300 bg-slate-900 px-2.5 py-1 rounded-lg border border-slate-800">
                        Avg Skill: <span class="font-bold text-sky-400">{{ $darkTeamSkill ?? 0 }}</span>
                    </div>
                @endrole
            </div>

            <ul class="space-y-2">
                @forelse(($darkTeamMembers ?? collect()) as $m)
                    @php
                        $isGoalie = !empty($m['is_goalie']);
                        $isEmptyNet = !empty($m['is_empty_net']);
                        $isCurrentUser = !empty($m['is_current_user']);
                    @endphp
                    <li class="rounded-xl px-3 py-2 border flex items-center justify-between gap-2 transition duration-150 @if($isCurrentUser) bg-emerald-950/80 border-emerald-400 text-white font-bold ring-1 ring-emerald-400/40 @elseif($isGoalie) bg-sky-950/60 border-sky-500/50 text-sky-200 font-semibold @else bg-slate-900/90 border-slate-800 hover:border-slate-700 text-slate-200 @endif @if($isEmptyNet) text-slate-500 italic @endif">
                        <span class="flex items-center gap-2 min-w-0">
                            @if($isGoalie && !$isEmptyNet)
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-sky-500/20 text-sky-300 border border-sky-500/40 shrink-0">G</span>
                            @endif
                            <span class="truncate text-sm font-medium">{{ $m['name'] }}</span>
                            @if(auth()->check() && auth()->user()->hasRole('admin') && empty($m['is_empty_net']))
                                <span class="text-[10px] font-mono rounded-md bg-slate-800 border border-slate-700 px-1.5 py-0.5 text-slate-400 shrink-0">Lvl {{ $m['level'] ?? 3 }}</span>
                            @endif
                        </span>

                        <span class="flex items-center gap-2 shrink-0">
                            @if($isCurrentUser)
                                <span class="text-[10px] font-mono rounded-full bg-emerald-500/20 border border-emerald-400/40 px-2 py-0.5 text-emerald-300 font-bold">You</span>
                            @endif
                            @if(auth()->check() && auth()->user()->hasRole('admin') && empty($m['is_empty_net']) && !empty($m['type']) && in_array($m['type'], ['user','guest']) && !empty($m['id']))
                                <div class="relative">
                                    <button class="player-options-btn px-2 py-1 rounded text-slate-400 hover:text-white hover:bg-slate-800 transition text-xs" title="Player actions">⋮</button>
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
    <div class="relative overflow-hidden rounded-3xl bg-white border-2 border-sky-300 shadow-lg p-5 flex flex-col justify-between transition-colors">
        <div>
            <div class="flex items-center justify-between pb-3.5 mb-3.5 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="w-3.5 h-3.5 rounded-full bg-white border-2 border-sky-500 shadow-sm"></span>
                    <div>
                        <h4 class="font-black text-base text-slate-900 tracking-wider uppercase font-mono">Light Team</h4>
                        <span class="text-[11px] text-slate-500 font-mono">{{ count($lightTeamMembers ?? []) }} skaters</span>
                    </div>
                </div>
                @role('admin')
                    <div class="text-[11px] font-mono text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">
                        Avg Skill: <span class="font-bold text-sky-600">{{ $lightTeamSkill ?? 0 }}</span>
                    </div>
                @endrole
            </div>

            <ul class="space-y-2">
                @forelse(($lightTeamMembers ?? collect()) as $m)
                    @php
                        $isGoalie = !empty($m['is_goalie']);
                        $isEmptyNet = !empty($m['is_empty_net']);
                        $isCurrentUser = !empty($m['is_current_user']);
                    @endphp
                    <li class="rounded-xl px-3 py-2 border flex items-center justify-between gap-2 transition duration-150 @if($isCurrentUser) bg-emerald-50 dark:bg-emerald-950/70 border-emerald-400 text-emerald-900 dark:text-white font-bold ring-1 ring-emerald-400/40 @elseif($isGoalie) bg-sky-50 dark:bg-cyan-950/40 border-sky-400 dark:border-cyan-500/40 text-sky-900 dark:text-cyan-200 font-semibold @else bg-slate-50 dark:bg-slate-950/80 border-slate-200 dark:border-slate-800 hover:border-sky-400 text-slate-800 dark:text-slate-200 @endif @if($isEmptyNet) text-slate-400 italic @endif">
                        <span class="flex items-center gap-2 min-w-0">
                            @if($isGoalie && !$isEmptyNet)
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-sky-500 text-white dark:bg-cyan-500/20 dark:text-cyan-300 shrink-0">G</span>
                            @endif
                            <span class="truncate text-sm font-medium">{{ $m['name'] }}</span>
                            @if(auth()->check() && auth()->user()->hasRole('admin') && empty($m['is_empty_net']))
                                <span class="text-[10px] font-mono rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-1.5 py-0.5 text-slate-600 dark:text-slate-400 shrink-0">Lvl {{ $m['level'] ?? 3 }}</span>
                            @endif
                        </span>

                        <span class="flex items-center gap-2 shrink-0">
                            @if($isCurrentUser)
                                <span class="text-[10px] font-mono rounded-full bg-emerald-100 dark:bg-emerald-500/20 border border-emerald-300 dark:border-emerald-400/40 px-2 py-0.5 text-emerald-800 dark:text-emerald-300 font-bold">You</span>
                            @endif
                            @if(auth()->check() && auth()->user()->hasRole('admin') && empty($m['is_empty_net']) && !empty($m['type']) && in_array($m['type'], ['user','guest']) && !empty($m['id']))
                                <div class="relative">
                                    <button class="player-options-btn px-2 py-1 rounded text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition text-xs" title="Player actions">⋮</button>
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
