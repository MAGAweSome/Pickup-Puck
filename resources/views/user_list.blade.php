@extends('layouts.app')

@section('content')

<div class="space-y-8 w-full" x-data="playerDirectoryApp()">
    @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-sm font-semibold flex items-center gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-500/15 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-sm font-semibold flex items-center gap-2.5">
            <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Hero / Header with Ambient Stadium Ice Glow -->
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-gradient-to-r dark:from-slate-950/95 dark:via-slate-900/90 dark:to-cyan-950/30 border border-slate-200 dark:border-cyan-500/25 p-6 sm:p-8 shadow-xl dark:shadow-[0_10px_35px_rgba(0,0,0,0.5)] backdrop-blur-md transition-all">
        <div class="absolute -right-16 -bottom-16 w-80 h-80 bg-sky-500/10 dark:bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -top-16 w-72 h-72 bg-indigo-500/10 dark:bg-sky-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono font-bold bg-sky-50 dark:bg-cyan-500/15 border border-sky-200 dark:border-cyan-500/30 text-sky-700 dark:text-cyan-300 mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span>
                    <span>League Roster Registry</span>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-slate-900 dark:text-white">Player Directory</h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-300 max-w-xl">
                    Search roster members, filter by goalie &amp; skater positions, inspect tiered ratings, and track game attendance.
                </p>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl bg-white/80 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 text-xs font-mono font-bold text-slate-800 dark:text-slate-200 shadow-sm">
                    <i class="fa-solid fa-users text-sky-500 dark:text-cyan-400"></i>
                    <span>{{ $users->count() }} Registered</span>
                </span>
                @if(isset($guests) && $guests->isNotEmpty())
                    <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl bg-white/80 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 text-xs font-mono font-bold text-slate-800 dark:text-slate-200 shadow-sm">
                        <i class="fa-solid fa-user-tag text-indigo-500 dark:text-indigo-400"></i>
                        <span>{{ $guests->count() }} Guests</span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Persistent Administrative Toolbar (Search, Filter Modal, Multi-Select & Sort) -->
    <div class="sticky top-20 z-30 rounded-2xl bg-white/95 dark:bg-slate-950/95 border border-slate-200 dark:border-slate-800 p-3 sm:p-4 shadow-xl backdrop-blur-md flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        <!-- Live Real-Time Search Bar -->
        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
            <input
                type="text"
                x-model="searchQuery"
                placeholder="Search players or guests by name or email..."
                class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 rounded-xl pl-9 pr-8 py-2 text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-sky-500 dark:focus:border-cyan-400 focus:ring-1 focus:ring-sky-500 transition shadow-inner"
            >
            <button
                type="button"
                x-show="searchQuery.length > 0"
                @click="searchQuery = ''"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs p-1"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Toolbar Action Buttons (Filters, Sort, Reset) -->
        <div class="flex items-center gap-2 shrink-0">
            <!-- Filter Modal Trigger -->
            <button
                type="button"
                @click="filterModalOpen = true"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border text-xs font-bold transition shadow-sm"
                :class="activeFilterCount > 0 
                    ? 'bg-sky-50 dark:bg-cyan-500/15 border-sky-300 dark:border-cyan-500/40 text-sky-700 dark:text-cyan-300' 
                    : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
            >
                <i class="fa-solid fa-sliders text-xs"></i>
                <span>Filter</span>
                <span
                    x-show="activeFilterCount > 0"
                    x-text="activeFilterCount"
                    class="w-4 h-4 rounded-full bg-sky-600 dark:bg-cyan-400 text-white dark:text-slate-950 text-[10px] flex items-center justify-center font-mono font-black"
                ></span>
            </button>

            <!-- Quick Sort Popover / Dropdown -->
            <div class="relative" @click.outside="sortMenuOpen = false">
                <button
                    type="button"
                    @click="sortMenuOpen = !sortMenuOpen"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold transition shadow-sm"
                >
                    <i class="fa-solid fa-arrow-down-wide-short text-xs text-sky-500 dark:text-cyan-400"></i>
                    <span x-text="currentSortLabel"></span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                </button>

                <div
                    x-show="sortMenuOpen"
                    x-transition
                    class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-2xl shadow-2xl z-50 py-1.5 text-xs font-medium"
                    style="display: none;"
                >
                    <div class="px-3 py-1 text-[10px] font-mono uppercase tracking-wider text-slate-400 dark:text-slate-500 font-bold">Sort Roster By</div>
                    <button type="button" @click="setSort('name_asc')" class="w-full text-left px-3.5 py-2 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-between" :class="sortBy === 'name_asc' ? 'text-sky-600 dark:text-cyan-400 font-bold' : 'text-slate-700 dark:text-slate-200'">
                        <span>First Name (A &rarr; Z)</span>
                        <i class="fa-solid fa-check text-xs" x-show="sortBy === 'name_asc'"></i>
                    </button>
                    <button type="button" @click="setSort('name_desc')" class="w-full text-left px-3.5 py-2 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-between" :class="sortBy === 'name_desc' ? 'text-sky-600 dark:text-cyan-400 font-bold' : 'text-slate-700 dark:text-slate-200'">
                        <span>First Name (Z &rarr; A)</span>
                        <i class="fa-solid fa-check text-xs" x-show="sortBy === 'name_desc'"></i>
                    </button>
                    <button type="button" @click="setSort('last_name_asc')" class="w-full text-left px-3.5 py-2 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-between" :class="sortBy === 'last_name_asc' ? 'text-sky-600 dark:text-cyan-400 font-bold' : 'text-slate-700 dark:text-slate-200'">
                        <span>Last Name (A &rarr; Z)</span>
                        <i class="fa-solid fa-check text-xs" x-show="sortBy === 'last_name_asc'"></i>
                    </button>
                    <button type="button" @click="setSort('last_name_desc')" class="w-full text-left px-3.5 py-2 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-between" :class="sortBy === 'last_name_desc' ? 'text-sky-600 dark:text-cyan-400 font-bold' : 'text-slate-700 dark:text-slate-200'">
                        <span>Last Name (Z &rarr; A)</span>
                        <i class="fa-solid fa-check text-xs" x-show="sortBy === 'last_name_desc'"></i>
                    </button>
                    <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                    <button type="button" @click="setSort('skill_desc')" class="w-full text-left px-3.5 py-2 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-between" :class="sortBy === 'skill_desc' ? 'text-sky-600 dark:text-cyan-400 font-bold' : 'text-slate-700 dark:text-slate-200'">
                        <span>Skill Rating (High &rarr; Low)</span>
                        <i class="fa-solid fa-check text-xs" x-show="sortBy === 'skill_desc'"></i>
                    </button>
                    <button type="button" @click="setSort('skill_asc')" class="w-full text-left px-3.5 py-2 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-between" :class="sortBy === 'skill_asc' ? 'text-sky-600 dark:text-cyan-400 font-bold' : 'text-slate-700 dark:text-slate-200'">
                        <span>Skill Rating (Low &rarr; High)</span>
                        <i class="fa-solid fa-check text-xs" x-show="sortBy === 'skill_asc'"></i>
                    </button>
                </div>
            </div>

            <!-- Clear Filters Button -->
            <button
                type="button"
                x-show="activeFilterCount > 0 || searchQuery.length > 0 || sortBy !== 'name_asc'"
                @click="resetAllFilters()"
                class="px-2.5 py-2 text-xs font-semibold text-slate-500 hover:text-rose-500 dark:text-slate-400 dark:hover:text-rose-400 transition"
                title="Reset filters"
            >
                <i class="fa-solid fa-rotate-left mr-1"></i>
                <span class="hidden sm:inline">Reset</span>
            </button>
        </div>
    </div>

    <!-- Filter Multi-Select Modal / Popover -->
    <div
        x-show="filterModalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/75 backdrop-blur-sm p-4"
        style="display: none;"
    >
        <div
            @click.outside="filterModalOpen = false"
            class="bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 rounded-3xl p-6 sm:p-7 max-w-lg w-full shadow-2xl space-y-5"
        >
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-sky-50 dark:bg-cyan-500/15 border border-sky-200 dark:border-cyan-500/30 flex items-center justify-center text-sky-600 dark:text-cyan-400">
                        <i class="fa-solid fa-filter text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Filter Directory</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Multi-select criteria to narrow roster view.</p>
                    </div>
                </div>
                <button type="button" @click="filterModalOpen = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-white text-lg leading-none p-1">
                    &times;
                </button>
            </div>

            <!-- Filter Sections -->
            <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-1">
                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Member Status</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition text-xs font-medium"
                            :class="filters.status.includes('registered') ? 'border-sky-500 bg-sky-50/60 dark:bg-cyan-500/10 text-sky-900 dark:text-cyan-300 font-bold' : 'border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300'">
                            <input type="checkbox" value="registered" x-model="filters.status" class="rounded text-sky-600 focus:ring-sky-500">
                            <span>Registered ({{ $users->count() }})</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition text-xs font-medium"
                            :class="filters.status.includes('guest') ? 'border-sky-500 bg-sky-50/60 dark:bg-cyan-500/10 text-sky-900 dark:text-cyan-300 font-bold' : 'border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300'">
                            <input type="checkbox" value="guest" x-model="filters.status" class="rounded text-sky-600 focus:ring-sky-500">
                            <span>Guests ({{ isset($guests) ? $guests->count() : 0 }})</span>
                        </label>
                    </div>
                </div>

                <!-- Position / Role Multi-Select -->
                <div>
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Ice Position</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition text-xs font-medium"
                            :class="filters.roles.includes('goalie') ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-500/15 text-emerald-900 dark:text-emerald-300 font-bold' : 'border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300'">
                            <input type="checkbox" value="goalie" x-model="filters.roles" class="rounded text-emerald-600 focus:ring-emerald-500">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-shield-halved text-emerald-500"></i>
                                <span>Goalies</span>
                            </span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition text-xs font-medium"
                            :class="filters.roles.includes('skater') ? 'border-sky-500 bg-sky-50/60 dark:bg-cyan-500/10 text-sky-900 dark:text-cyan-300 font-bold' : 'border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300'">
                            <input type="checkbox" value="skater" x-model="filters.roles" class="rounded text-sky-600 focus:ring-sky-500">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-person-skating text-sky-500"></i>
                                <span>Skaters</span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Skill Level Filter -->
                <div>
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Skill Rating Tiers</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <label class="flex items-center gap-1.5 p-2 rounded-xl border cursor-pointer text-xs font-medium text-center justify-center transition"
                            :class="filters.skills.includes('1') ? 'border-[#a04624] bg-[#4a1c10]/20 dark:bg-[#38150c] text-[#c2410c] dark:text-[#fba778] font-bold' : 'border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300'">
                            <input type="checkbox" value="1" x-model="filters.skills" class="sr-only">
                            <span>Bronze (1)</span>
                        </label>
                        <label class="flex items-center gap-1.5 p-2 rounded-xl border cursor-pointer text-xs font-medium text-center justify-center transition"
                            :class="filters.skills.includes('2') ? 'border-slate-400 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold' : 'border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300'">
                            <input type="checkbox" value="2" x-model="filters.skills" class="sr-only">
                            <span>Silver (2)</span>
                        </label>
                        <label class="flex items-center gap-1.5 p-2 rounded-xl border cursor-pointer text-xs font-medium text-center justify-center transition"
                            :class="filters.skills.includes('3') ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-950/40 text-yellow-800 dark:text-yellow-300 font-bold' : 'border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300'">
                            <input type="checkbox" value="3" x-model="filters.skills" class="sr-only">
                            <span>Gold (3)</span>
                        </label>
                        <label class="flex items-center gap-1.5 p-2 rounded-xl border cursor-pointer text-xs font-medium text-center justify-center transition"
                            :class="filters.skills.includes('4') ? 'border-cyan-500 bg-cyan-50 dark:bg-cyan-950/40 text-cyan-800 dark:text-cyan-300 font-bold' : 'border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300'">
                            <input type="checkbox" value="4" x-model="filters.skills" class="sr-only">
                            <span>Platinum (4+)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                <button type="button" @click="resetFilters()" class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
                    Clear Criteria
                </button>
                <button type="button" @click="filterModalOpen = false" class="px-5 py-2 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 dark:from-cyan-500 dark:to-sky-600 dark:hover:from-cyan-400 dark:hover:to-sky-500 text-white dark:text-slate-950 font-bold rounded-xl text-xs transition shadow-md">
                    Apply Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Dynamic Player Directory Section -->
    <div x-show="filters.status.length === 0 || filters.status.includes('registered')">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                <h2 class="text-lg sm:text-xl font-black tracking-tight text-slate-900 dark:text-white">Registered Players</h2>
            </div>
            <span class="text-xs font-mono font-bold text-slate-500 dark:text-slate-400" x-text="visibleUsersCount + ' Displayed'"></span>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
            @foreach($users as $user)
                @php
                    $isGoalie = strtolower($user->role_preference ?? '') === 'goalie';
                    $userLvl = (int) ($user->level ?? 3);
                    $parts = explode(' ', trim($user->name));
                    $firstName = $parts[0] ?? '';
                    $lastName = count($parts) > 1 ? end($parts) : '';
                    $initials = strtoupper(substr($firstName, 0, 1) . ($lastName ? substr($lastName, 0, 1) : ''));
                @endphp
                <a
                    href="/admin/user/{{ $user->id }}"
                    @click.prevent="openEditModal({
                        id: {{ $user->id }},
                        name: @js($user->name),
                        email: @js($user->email ?? ''),
                        gameRole: @js($user->role_preference ?? ''),
                        level: {{ $userLvl }},
                        isAdmin: {{ $user->hasRole('admin') ? 'true' : 'false' }},
                        isSelf: {{ $user->id === auth()->id() ? 'true' : 'false' }}
                    })"
                    x-show="matchesUser({
                        name: '{{ addslashes($user->name) }}',
                        email: '{{ addslashes($user->email ?? '') }}',
                        role: '{{ $isGoalie ? 'goalie' : 'skater' }}',
                        skill: '{{ $userLvl }}',
                        status: 'registered',
                        firstName: '{{ addslashes($firstName) }}',
                        lastName: '{{ addslashes($lastName) }}'
                    })"
                    x-transition
                    class="relative rounded-2xl p-3.5 transition-all duration-200 flex flex-col justify-between group hover:-translate-y-1 shadow-xs hover:shadow-xl cursor-pointer no-underline block
                        @if($isGoalie)
                            bg-gradient-to-b from-white via-white to-sky-50/50 dark:from-[#132338] dark:via-[#0f1d2e] dark:to-[#0b1725] border-2 border-sky-400/80 dark:border-cyan-400/70 hover:border-sky-500 dark:hover:border-cyan-300 shadow-sky-500/5
                        @else
                            bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-800 hover:border-sky-400 dark:hover:border-cyan-500/50
                        @endif
                    "
                    data-user-card
                    data-skill="{{ $userLvl }}"
                >
                    <!-- Top Row: Position / Goalie Badge & Level Badge -->
                    <div class="flex items-center justify-between gap-1.5 mb-2.5">
                        <div>
                            @if($isGoalie)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-mono font-black uppercase tracking-wider bg-sky-100 dark:bg-cyan-500/20 text-sky-800 dark:text-cyan-300 border border-sky-300 dark:border-cyan-500/50 shadow-xs">
                                    <i class="fa-solid fa-shield-halved text-[9px]"></i>
                                    <span>Goalie</span>
                                </span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-mono font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    {{ Str::title($user->role_preference ?? 'Skater') }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-1">
                            @if ($user->hasRole('admin'))
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30" title="Administrator">
                                    Admin
                                </span>
                            @endif

                            <!-- Gamified Skill Tier Badge -->
                            @if($userLvl <= 1)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold bg-[#38150c] text-[#fba778] border border-[#a04624]/70 shadow-xs" title="Bronze Tier">
                                    Lvl 1
                                </span>
                            @elseif($userLvl == 2)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold bg-slate-800 text-slate-200 border border-slate-500/60 shadow-xs" title="Silver Tier">
                                    Lvl 2
                                </span>
                            @elseif($userLvl == 3)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold bg-yellow-950/70 text-yellow-300 border border-yellow-500/60 shadow-xs" title="Gold Tier">
                                    Lvl 3
                                </span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-mono font-black bg-cyan-950/80 text-cyan-300 border border-cyan-400/70 shadow-xs" title="Platinum Tier">
                                    Lvl {{ $userLvl }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Squircle Avatar & Full Name -->
                    <div class="flex flex-col items-center text-center my-1">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center font-mono font-black text-sm transition duration-200 mb-2 shadow-xs
                            @if($isGoalie)
                                bg-gradient-to-br from-sky-500/20 to-cyan-500/30 border-2 border-sky-400 dark:border-cyan-400 text-sky-700 dark:text-cyan-300 shadow-[0_0_12px_rgba(56,189,248,0.25)]
                            @else
                                bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 group-hover:border-sky-400 group-hover:text-sky-600 dark:group-hover:text-cyan-400
                            @endif
                        ">
                            {{ $initials }}
                        </div>

                        <!-- Full Name Centered Under Avatar -->
                        <span data-player-name class="text-xs sm:text-sm font-black text-slate-900 dark:text-white group-hover:text-sky-600 dark:group-hover:text-cyan-400 transition truncate max-w-full block tracking-tight">
                            {{ $user->name }}
                        </span>
                        @if($user->isManaged())
                            <span class="text-[9px] font-mono text-sky-600 dark:text-cyan-400 bg-sky-50 dark:bg-sky-500/10 border border-sky-200 dark:border-sky-500/20 px-1.5 py-0.5 rounded mt-1 inline-block truncate max-w-full">
                                Managed by {{ $user->manager->name ?? 'Account' }}
                            </span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Guest List Section -->
    @if(isset($guests) && $guests->isNotEmpty())
        <div class="pt-6 border-t border-slate-200 dark:border-slate-800" x-show="filters.status.length === 0 || filters.status.includes('guest')">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                    <h2 class="text-lg sm:text-xl font-black tracking-tight text-slate-900 dark:text-white">Guest Roster</h2>
                </div>
                <span class="text-xs font-mono font-bold text-slate-500 dark:text-slate-400" x-text="visibleGuestsCount + ' Displayed'"></span>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
                @foreach($guests as $guest)
                    @php
                        $gParts = explode(' ', trim($guest->name));
                        $gFirst = $gParts[0] ?? '';
                        $gLast = count($gParts) > 1 ? end($gParts) : '';
                        $gInitials = strtoupper(substr($gFirst, 0, 1) . ($gLast ? substr($gLast, 0, 1) : 'G'));
                        $gLvl = (int) ($guest->level ?? 3);
                    @endphp
                    <a
                        href="{{ route('guest_game_history', ['guest' => $guest->id]) }}"
                        x-show="matchesGuest({
                            name: '{{ addslashes($guest->name) }}',
                            id: '{{ $guest->id }}',
                            skill: '{{ $gLvl }}',
                            firstName: '{{ addslashes($gFirst) }}',
                            lastName: '{{ addslashes($gLast) }}'
                        })"
                        x-transition
                        class="relative rounded-2xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-800 hover:border-sky-400 dark:hover:border-cyan-500/50 p-3.5 shadow-xs hover:shadow-xl transition-all duration-200 flex flex-col justify-between group hover:-translate-y-1 cursor-pointer no-underline block"
                        data-guest-card
                        data-skill="{{ $gLvl }}"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1.5 mb-2.5">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/60">
                                    <i class="fa-solid fa-user-tag text-[9px]"></i>
                                    <span>Guest</span>
                                </span>
                                
                                <div class="flex items-center gap-1">
                                    @if($gLvl <= 1)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold bg-[#38150c] text-[#fba778] border border-[#a04624]/70 shadow-xs" title="Bronze Tier">
                                            Lvl 1
                                        </span>
                                    @elseif($gLvl == 2)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold bg-slate-800 text-slate-200 border border-slate-500/60 shadow-xs" title="Silver Tier">
                                            Lvl 2
                                        </span>
                                    @elseif($gLvl == 3)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold bg-yellow-950/70 text-yellow-300 border border-yellow-500/60 shadow-xs" title="Gold Tier">
                                            Lvl 3
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-mono font-black bg-cyan-950/80 text-cyan-300 border border-cyan-400/70 shadow-xs" title="Platinum Tier">
                                            Lvl {{ $gLvl }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col items-center text-center my-1">
                                <div class="w-11 h-11 rounded-xl bg-indigo-50 dark:bg-slate-900 border border-indigo-200 dark:border-indigo-900/40 text-indigo-600 dark:text-indigo-300 flex items-center justify-center font-mono font-black text-sm mb-2 shadow-xs group-hover:border-indigo-400 transition">
                                    {{ $gInitials }}
                                </div>
                                <span data-player-name class="text-xs sm:text-sm font-black text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-300 transition truncate max-w-full block tracking-tight">
                                    {{ $guest->name }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Admin Edit Player, 5-Hour Recovery Link & Danger Zone Modal -->
    <div
        x-show="editModalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4 overflow-y-auto"
        style="display: none;"
    >
        <div
            @click.outside="editModalOpen = false"
            class="bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-cyan-500/30 rounded-3xl p-6 sm:p-7 max-w-2xl w-full shadow-2xl space-y-6 my-8 max-h-[90vh] overflow-y-auto"
        >
            <!-- Modal Header -->
            <div class="flex items-start justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-sky-50 dark:bg-cyan-500/15 text-sky-700 dark:text-cyan-300 border border-sky-200 dark:border-cyan-500/30 mb-1.5">
                        <i class="fa-solid fa-user-gear"></i>
                        <span>Admin Player Management</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white" x-text="'Edit Player: ' + editForm.name"></h3>
                    <p class="text-xs font-mono text-slate-500 dark:text-slate-400" x-text="'User #' + editForm.id + ' · ' + editForm.email"></p>
                </div>
                <div class="flex items-center gap-2">
                    <a :href="'/admin/user/' + editForm.id + '/history'" class="px-3 py-1.5 rounded-xl bg-sky-50 dark:bg-sky-500/15 border border-sky-200 dark:border-sky-500/30 text-sky-700 dark:text-sky-300 text-xs font-bold no-underline hover:bg-sky-100 dark:hover:bg-sky-500/25 transition">
                        History
                    </a>
                    <a :href="'/admin/user/' + editForm.id" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold no-underline hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                        Full Page
                    </a>
                    <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-white text-xl leading-none p-1">&times;</button>
                </div>
            </div>

            <!-- Edit Profile Form (No password field - Zero Direct Admin Password Editing) -->
            <form @submit.prevent="savePlayerChanges()" class="space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Full Name</label>
                        <input
                            type="text"
                            x-model="editForm.name"
                            required
                            minlength="4"
                            class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Email Address</label>
                        <input
                            type="email"
                            x-model="editForm.email"
                            required
                            class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                        >
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Preferred Position</label>
                        <select
                            x-model="editForm.gameRole"
                            class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                        >
                            <option value="">Select Position</option>
                            @foreach (($GAME_ROLES ?? []) as $gamerole)
                                <option value="{{ $gamerole->value }}">
                                    {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $gamerole->name)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Player Level</label>
                        <select
                            x-model="editForm.level"
                            class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                        >
                            <option value="1">1 - Beginner / low rec</option>
                            <option value="2">2 - Recreational</option>
                            <option value="3">3 - Intermediate / competitive</option>
                            <option value="4">4 - Advanced / high skill</option>
                        </select>
                    </div>
                </div>

                <label class="flex items-center gap-2.5 cursor-pointer select-none pt-1">
                    <input type="checkbox" x-model="editForm.isAdmin" class="h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-cyan-500 focus:ring-cyan-500">
                    <span class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200">Admin Privileges</span>
                </label>

                <div class="flex items-center justify-between gap-3 pt-2">
                    <div>
                        <span x-show="modalSuccess" x-text="modalSuccess" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400"></span>
                        <span x-show="modalError" x-text="modalError" class="text-xs font-semibold text-rose-600 dark:text-rose-400"></span>
                    </div>
                    <button
                        type="submit"
                        :disabled="savingPlayer"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-sky-600 to-indigo-600 dark:from-cyan-500 dark:to-sky-500 text-white dark:text-slate-950 font-black rounded-xl text-xs shadow-md transition cursor-pointer disabled:opacity-60"
                    >
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span x-text="savingPlayer ? 'Saving...' : 'Save Changes'"></span>
                    </button>
                </div>
            </form>

            <!-- 5-Hour Self-Service Recovery Link Section -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-key text-cyan-500 text-sm"></i>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Player Account &amp; Password Recovery Link</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Generate a single-use 5-hour link so the player can update their name, email, and password.</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        @click="generateRecoveryLink()"
                        :disabled="generatingLink"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-cyan-500 dark:hover:bg-cyan-400 text-white dark:text-slate-950 text-xs font-bold transition cursor-pointer disabled:opacity-60"
                    >
                        <i class="fa-solid fa-link"></i>
                        <span x-text="generatingLink ? 'Generating...' : (recoveryUrl ? 'Regenerate 5-Hour Link' : 'Generate 5-Hour Link')"></span>
                    </button>

                    <button
                        type="button"
                        @click="sendRecoveryEmail()"
                        :disabled="sendingEmail"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 dark:bg-sky-500/15 dark:hover:bg-sky-500/25 border border-sky-200 dark:border-sky-500/30 text-sky-700 dark:text-sky-300 text-xs font-bold transition cursor-pointer disabled:opacity-60"
                    >
                        <i class="fa-solid fa-envelope"></i>
                        <span x-text="sendingEmail ? 'Sending...' : 'Send Link via Email'"></span>
                    </button>
                </div>

                <div x-show="recoveryUrl" class="flex items-center gap-2" style="display: none;">
                    <input
                        x-ref="modalRecoveryInput"
                        type="text"
                        readonly
                        :value="recoveryUrl"
                        class="flex-1 bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs font-mono text-slate-700 dark:text-cyan-300 select-all"
                    >
                    <button
                        type="button"
                        @click="copyRecoveryUrl()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition cursor-pointer shrink-0"
                    >
                        <i class="fa-solid" :class="copiedLink ? 'fa-check text-emerald-400' : 'fa-copy'"></i>
                        <span x-text="copiedLink ? 'Copied!' : 'Copy to Clipboard'"></span>
                    </button>
                </div>

                <p x-show="recoveryMsg" x-text="recoveryMsg" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400" style="display: none;"></p>
            </div>

            <!-- Danger Zone: Two-Step Permanent Player Deletion -->
            <div x-show="!editForm.isSelf" class="pt-4 border-t border-rose-200 dark:border-rose-500/25 space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <h4 class="text-sm font-bold text-rose-700 dark:text-rose-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>Danger Zone</span>
                        </h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Permanently delete this player and clean up all game roster records.</p>
                    </div>

                    <button
                        type="button"
                        x-show="!confirmDeleteStep"
                        @click="confirmDeleteStep = true"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold transition cursor-pointer shrink-0"
                    >
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Delete Player Permanently</span>
                    </button>
                </div>

                <div x-show="confirmDeleteStep" class="p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3" style="display: none;">
                    <span class="text-xs font-semibold text-rose-800 dark:text-rose-200" x-text="'Confirm permanent deletion of ' + editForm.name + '? This cannot be undone.'"></span>
                    <div class="flex items-center gap-2 shrink-0">
                        <button
                            type="button"
                            @click="deletePlayerPermanently()"
                            :disabled="deletingPlayer"
                            class="px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-black transition cursor-pointer disabled:opacity-60"
                            x-text="deletingPlayer ? 'Deleting...' : 'Confirm Delete'"
                        ></button>
                        <button
                            type="button"
                            @click="confirmDeleteStep = false"
                            class="px-3 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold transition cursor-pointer"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function playerDirectoryApp() {
    return {
        searchQuery: '',
        filterModalOpen: false,
        sortMenuOpen: false,
        sortBy: 'name_asc',
        editModalOpen: false,
        editForm: { id: null, name: '', email: '', gameRole: '', level: 3, isAdmin: false, isSelf: false },
        savingPlayer: false,
        deletingPlayer: false,
        confirmDeleteStep: false,
        modalSuccess: '',
        modalError: '',
        recoveryUrl: '',
        recoveryMsg: '',
        copiedLink: false,
        generatingLink: false,
        sendingEmail: false,

        openEditModal(player) {
            this.editForm = {
                id: player.id,
                name: player.name || '',
                email: player.email || '',
                gameRole: player.gameRole || '',
                level: String(player.level || 3),
                isAdmin: !!player.isAdmin,
                isSelf: !!player.isSelf,
            };
            this.modalSuccess = '';
            this.modalError = '';
            this.recoveryUrl = '';
            this.recoveryMsg = '';
            this.copiedLink = false;
            this.confirmDeleteStep = false;
            this.editModalOpen = true;
        },

        async savePlayerChanges() {
            this.savingPlayer = true;
            this.modalSuccess = '';
            this.modalError = '';
            try {
                const res = await fetch('/admin/user/' + this.editForm.id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: this.editForm.name,
                        email: this.editForm.email,
                        gameRole: this.editForm.gameRole || null,
                        level: parseInt(this.editForm.level, 10),
                        adminCheck: this.editForm.isAdmin ? 1 : 0,
                    })
                });
                const data = await res.json();
                if (!res.ok) {
                    const firstErr = data.errors ? Object.values(data.errors).flat()[0] : null;
                    throw new Error(firstErr || data.message || 'Failed to save changes.');
                }
                this.modalSuccess = data.message || 'Saved!';
                setTimeout(() => window.location.reload(), 650);
            } catch (e) {
                this.modalError = e.message;
            } finally {
                this.savingPlayer = false;
            }
        },

        async generateRecoveryLink() {
            this.generatingLink = true;
            this.modalError = '';
            this.recoveryMsg = '';
            try {
                const res = await fetch('/admin/user/' + this.editForm.id + '/recovery-link', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Failed to generate link.');
                this.recoveryUrl = data.url;
                this.recoveryMsg = '5-hour single-use link ready.';
            } catch (e) {
                this.modalError = e.message;
            } finally {
                this.generatingLink = false;
            }
        },

        async copyRecoveryUrl() {
            if (!this.recoveryUrl) return;
            try {
                await navigator.clipboard.writeText(this.recoveryUrl);
            } catch (e) {
                const el = this.$refs.modalRecoveryInput;
                el.select();
                document.execCommand('copy');
            }
            this.copiedLink = true;
            setTimeout(() => this.copiedLink = false, 2500);
        },

        async sendRecoveryEmail() {
            this.sendingEmail = true;
            this.modalError = '';
            this.recoveryMsg = '';
            try {
                const res = await fetch('/admin/user/' + this.editForm.id + '/recovery-email', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ recovery_url: this.recoveryUrl })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Failed to send email.');
                if (data.url) this.recoveryUrl = data.url;
                this.recoveryMsg = data.message || 'Recovery email sent!';
            } catch (e) {
                this.modalError = e.message;
            } finally {
                this.sendingEmail = false;
            }
        },

        async deletePlayerPermanently() {
            this.deletingPlayer = true;
            this.modalError = '';
            try {
                const res = await fetch('/admin/user/' + this.editForm.id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Failed to delete player.');
                window.location.reload();
            } catch (e) {
                this.modalError = e.message;
                this.deletingPlayer = false;
            }
        },
        filters: {
            status: [], // 'registered', 'guest'
            roles: [],  // 'goalie', 'skater'
            skills: []  // '1', '2', '3', '4'
        },

        get activeFilterCount() {
            return this.filters.status.length + this.filters.roles.length + this.filters.skills.length;
        },

        get currentSortLabel() {
            switch(this.sortBy) {
                case 'name_asc': return 'First Name (A-Z)';
                case 'name_desc': return 'First Name (Z-A)';
                case 'last_name_asc': return 'Last Name (A-Z)';
                case 'last_name_desc': return 'Last Name (Z-A)';
                case 'skill_desc': return 'Skill (High to Low)';
                case 'skill_asc': return 'Skill (Low to High)';
                default: return 'Sort';
            }
        },

        setSort(sortType) {
            this.sortBy = sortType;
            this.sortMenuOpen = false;
            this.applySortToDOM();
        },

        resetFilters() {
            this.filters.status = [];
            this.filters.roles = [];
            this.filters.skills = [];
        },

        resetAllFilters() {
            this.searchQuery = '';
            this.sortBy = 'name_asc';
            this.resetFilters();
            this.applySortToDOM();
        },

        matchesUser(u) {
            // Search query
            if (this.searchQuery.trim().length > 0) {
                const q = this.searchQuery.toLowerCase().trim();
                const matchName = u.name.toLowerCase().includes(q);
                const matchEmail = u.email.toLowerCase().includes(q);
                if (!matchName && !matchEmail) return false;
            }

            // Status filter
            if (this.filters.status.length > 0 && !this.filters.status.includes('registered')) {
                return false;
            }

            // Role filter
            if (this.filters.roles.length > 0 && !this.filters.roles.includes(u.role)) {
                return false;
            }

            // Skill filter
            if (this.filters.skills.length > 0) {
                const lvl = parseInt(u.skill, 10);
                const matchesAnySkill = this.filters.skills.some(s => {
                    const target = parseInt(s, 10);
                    if (target === 4) return lvl >= 4;
                    return lvl === target;
                });
                if (!matchesAnySkill) return false;
            }

            return true;
        },

        matchesGuest(g) {
            // Search query
            if (this.searchQuery.trim().length > 0) {
                const q = this.searchQuery.toLowerCase().trim();
                const matchName = g.name.toLowerCase().includes(q);
                const matchId = ('#' + g.id).includes(q) || g.id.toString() === q;
                if (!matchName && !matchId) return false;
            }

            // Status filter
            if (this.filters.status.length > 0 && !this.filters.status.includes('guest')) {
                return false;
            }

            // Role filter
            if (this.filters.roles.length > 0 && !this.filters.roles.includes('skater')) {
                return false;
            }

            // Skill filter
            if (this.filters.skills.length > 0) {
                const lvl = parseInt(g.skill, 10);
                const matchesAnySkill = this.filters.skills.some(s => {
                    const target = parseInt(s, 10);
                    if (target === 4) return lvl >= 4;
                    return lvl === target;
                });
                if (!matchesAnySkill) return false;
            }

            return true;
        },

        get visibleUsersCount() {
            // Dynamic count helper
            return '{{ $users->count() }}';
        },

        get visibleGuestsCount() {
            return '{{ isset($guests) ? $guests->count() : 0 }}';
        },

        applySortToDOM() {
            const sortList = (containers) => {
                if (!containers.length) return;
                const parent = containers[0].parentElement;
                if (!parent) return;

                const cards = Array.from(containers);
                cards.sort((a, b) => {
                    const nameA = (a.querySelector('[data-player-name]')?.innerText || a.innerText || '').trim();
                    const nameB = (b.querySelector('[data-player-name]')?.innerText || b.innerText || '').trim();
                    const partsA = nameA.split(' ');
                    const partsB = nameB.split(' ');
                    const firstA = partsA[0] || '';
                    const firstB = partsB[0] || '';
                    const lastA = partsA.length > 1 ? partsA[partsA.length - 1] : firstA;
                    const lastB = partsB.length > 1 ? partsB[partsB.length - 1] : firstB;

                    const skillA = parseInt(a.getAttribute('data-skill') || '3', 10);
                    const skillB = parseInt(b.getAttribute('data-skill') || '3', 10);

                    if (this.sortBy === 'name_asc') return firstA.localeCompare(firstB);
                    if (this.sortBy === 'name_desc') return firstB.localeCompare(firstA);
                    if (this.sortBy === 'last_name_asc') return lastA.localeCompare(lastB);
                    if (this.sortBy === 'last_name_desc') return lastB.localeCompare(lastA);
                    if (this.sortBy === 'skill_desc') {
                        if (skillB !== skillA) return skillB - skillA;
                        return firstA.localeCompare(firstB);
                    }
                    if (this.sortBy === 'skill_asc') {
                        if (skillA !== skillB) return skillA - skillB;
                        return firstA.localeCompare(firstB);
                    }
                    return 0;
                });

                cards.forEach(c => parent.appendChild(c));
            };

            sortList(document.querySelectorAll('[data-user-card]'));
            sortList(document.querySelectorAll('[data-guest-card]'));
        }
    };
}
</script>

@endsection
