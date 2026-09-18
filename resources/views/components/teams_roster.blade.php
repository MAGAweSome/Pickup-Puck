@if(!empty($currentUserTeam))
    <div class="mb-4 text-sm animate-pulse">
        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-500/10 border border-emerald-400/30 px-3.5 py-1.5 text-emerald-200 shadow-sm">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            <span class="font-semibold">You</span>
            <span class="text-emerald-200/90">are on</span>
            <span class="font-extrabold uppercase tracking-wide text-white bg-emerald-600/60 px-2 py-0.5 rounded">{{ $currentUserTeam }}</span>
        </span>
    </div>
@endif

<div class="grid md:grid-cols-2 gap-4">
    <!-- Dark Team -->
    <div class="bg-slate-900/90 border border-slate-700/80 rounded-lg p-3.5 shadow-md">
        <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-700/60">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-slate-600 border border-slate-400 shadow-sm"></span>
                <span class="font-bold text-base text-slate-200 tracking-wide uppercase">Dark</span>
                <span class="text-xs text-slate-400">({{ count($darkTeamMembers ?? []) }} skaters)</span>
            </div>
            @role('admin')
                <div class="text-xs text-slate-400">Skill Level: <span class="font-semibold text-ice">{{ $darkTeamSkill ?? 0 }}</span></div>
            @endrole
        </div>
        <ul class="space-y-1.5 text-ice">
            @forelse(($darkTeamMembers ?? collect()) as $m)
                @php
                    $isGoalie = !empty($m['is_goalie']);
                    $isEmptyNet = !empty($m['is_empty_net']);
                    $isCurrentUser = !empty($m['is_current_user']);
                @endphp
                <li class="rounded-md px-2.5 py-1.5 border flex items-center justify-between gap-2 transition-all duration-150 @if($isCurrentUser) bg-emerald-500/15 border-emerald-400/40 ring-1 ring-emerald-400/30 font-bold @elseif($isGoalie) bg-ice-blue/15 border-ice-blue/30 font-semibold @else bg-slate-800/40 border-slate-700/50 hover:bg-slate-800/80 @endif @if($isEmptyNet) text-slate-400 italic @else text-ice @endif">
                    <span class="flex items-center gap-1.5">
                        <span>{{ $m['name'] }}</span>
                        @if($isGoalie && !$isEmptyNet)
                            <span class="px-1.5 py-0.2 rounded text-[11px] font-bold uppercase tracking-wider bg-ice-blue/20 text-ice-blue border border-ice-blue/30">G</span>
                        @endif
                        @if(auth()->check() && auth()->user()->hasRole('admin') && empty($m['is_empty_net']))
                            <span class="ml-1 inline-flex items-center text-[10px] rounded-full bg-slate-700/50 border border-white/10 px-1.5 py-0.5 text-slate-300">Lvl {{ $m['level'] ?? 3 }}</span>
                        @endif
                    </span>
                    <span class="flex items-center gap-2">
                        @if($isCurrentUser)
                            <span class="text-xs rounded-full bg-emerald-400/20 border border-emerald-400/40 px-2 py-0.5 text-emerald-200 font-bold">You</span>
                        @endif
                        @if(auth()->check() && auth()->user()->hasRole('admin') && empty($m['is_empty_net']) && !empty($m['type']) && in_array($m['type'], ['user','guest']) && !empty($m['id']))
                            <div class="relative">
                                <button class="player-options-btn px-2 py-0.5 rounded text-slate-400 hover:text-white hover:bg-slate-700 transition" title="Player actions">⋮</button>
                                <div class="player-options-menu hidden absolute right-0 mt-2 w-44 bg-slate-900 border border-slate-700 rounded-lg shadow-xl z-50 py-1">
                                    <button data-member-type="{{ $m['type'] }}" data-member-id="{{ $m['id'] }}" data-target-team="2" class="w-full text-left px-3 py-2 text-sm text-slate-200 hover:bg-slate-800 admin-move-team">Move → Light</button>
                                </div>
                            </div>
                        @endif
                    </span>
                </li>
            @empty
                <li class="text-slate-400 text-sm py-2">Teams not generated yet.</li>
            @endforelse
        </ul>
    </div>

    <!-- Light Team -->
    <div class="bg-slate-900/90 border border-slate-700/80 rounded-lg p-3.5 shadow-md">
        <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-700/60">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-slate-100 border border-white shadow-sm"></span>
                <span class="font-bold text-base text-white tracking-wide uppercase">Light</span>
                <span class="text-xs text-slate-400">({{ count($lightTeamMembers ?? []) }} skaters)</span>
            </div>
            @role('admin')
                <div class="text-xs text-slate-400">Skill Level: <span class="font-semibold text-ice">{{ $lightTeamSkill ?? 0 }}</span></div>
            @endrole
        </div>
        <ul class="space-y-1.5 text-ice">
            @forelse(($lightTeamMembers ?? collect()) as $m)
                @php
                    $isGoalie = !empty($m['is_goalie']);
                    $isEmptyNet = !empty($m['is_empty_net']);
                    $isCurrentUser = !empty($m['is_current_user']);
                @endphp
                <li class="rounded-md px-2.5 py-1.5 border flex items-center justify-between gap-2 transition-all duration-150 @if($isCurrentUser) bg-emerald-500/15 border-emerald-400/40 ring-1 ring-emerald-400/30 font-bold @elseif($isGoalie) bg-ice-blue/15 border-ice-blue/30 font-semibold @else bg-slate-800/40 border-slate-700/50 hover:bg-slate-800/80 @endif @if($isEmptyNet) text-slate-400 italic @else text-ice @endif">
                    <span class="flex items-center gap-1.5">
                        <span>{{ $m['name'] }}</span>
                        @if($isGoalie && !$isEmptyNet)
                            <span class="px-1.5 py-0.2 rounded text-[11px] font-bold uppercase tracking-wider bg-ice-blue/20 text-ice-blue border border-ice-blue/30">G</span>
                        @endif
                        @if(auth()->check() && auth()->user()->hasRole('admin') && empty($m['is_empty_net']))
                            <span class="ml-1 inline-flex items-center text-[10px] rounded-full bg-slate-700/50 border border-white/10 px-1.5 py-0.5 text-slate-300">Lvl {{ $m['level'] ?? 3 }}</span>
                        @endif
                    </span>
                    <span class="flex items-center gap-2">
                        @if($isCurrentUser)
                            <span class="text-xs rounded-full bg-emerald-400/20 border border-emerald-400/40 px-2 py-0.5 text-emerald-200 font-bold">You</span>
                        @endif
                        @if(auth()->check() && auth()->user()->hasRole('admin') && empty($m['is_empty_net']) && !empty($m['type']) && in_array($m['type'], ['user','guest']) && !empty($m['id']))
                            <div class="relative">
                                <button class="player-options-btn px-2 py-0.5 rounded text-slate-400 hover:text-white hover:bg-slate-700 transition" title="Player actions">⋮</button>
                                <div class="player-options-menu hidden absolute right-0 mt-2 w-44 bg-slate-900 border border-slate-700 rounded-lg shadow-xl z-50 py-1">
                                    <button data-member-type="{{ $m['type'] }}" data-member-id="{{ $m['id'] }}" data-target-team="1" class="w-full text-left px-3 py-2 text-sm text-slate-200 hover:bg-slate-800 admin-move-team">Move → Dark</button>
                                </div>
                            </div>
                        @endif
                    </span>
                </li>
            @empty
                <li class="text-slate-400 text-sm py-2">Teams not generated yet.</li>
            @endforelse
        </ul>
    </div>
</div>
