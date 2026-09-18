@php
    $isHomeActive = request()->routeIs('home');
    $isGamesActive = request()->routeIs('games.*') || request()->routeIs('game_detail.*') || request()->is('game/*');
    $isProfileActive = request()->routeIs('profile*');
    $isAdminActive = request()->routeIs('user_list*') || request()->routeIs('settings.*');
@endphp

<header id="navigationBar" class="fixed top-0 left-0 right-0 z-50 h-16 bg-white/95 dark:bg-slate-950/95 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-[0_4px_25px_rgba(0,0,0,0.5)] transition-colors">
    <div class="w-full max-w-7xl 2xl:max-w-[1680px] 3xl:max-w-[1880px] mx-auto h-16 flex items-center justify-between px-3.5 sm:px-6 lg:px-8 2xl:px-10">
        
        <!-- Left Brand & Season Beacon -->
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ auth()->check() ? route('home') : '/' }}" class="flex items-center gap-2.5 group no-underline min-w-0">
                <img src="{{ asset('photos/Pickup Puck Logo.png') }}" alt="Pickup Puck" class="h-9 w-auto drop-shadow-sm dark:drop-shadow-[0_0_12px_rgba(56,189,248,0.4)] group-hover:scale-105 transition-transform duration-200">
                <div class="flex flex-col">
                    <span class="font-black tracking-wider text-sm text-slate-900 dark:text-white uppercase font-mono leading-tight flex items-center gap-1.5">
                        <span>Pickup Puck</span>
                        <span class="hidden sm:inline-flex w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                    </span>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium tracking-wide hidden sm:inline">Pickup Hockey League</span>
                </div>
            </a>
        </div>

        <!-- Center Desktop Navigation Pills (Hidden on mobile) -->
        <nav class="hidden lg:flex items-center gap-1 p-1 rounded-2xl bg-slate-100/80 dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-800/80">
            @if(auth()->check())
                <a href="{{ route('home') }}"
                    class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all no-underline {{ $isHomeActive ? 'bg-white dark:bg-slate-800 text-sky-600 dark:text-sky-400 shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white/50 dark:hover:bg-slate-800/50' }}">
                    <i class="fa-solid fa-house text-xs"></i>
                    <span>Dashboard</span>
                </a>

                <a id="sidebarGamesLink" href="{{ route('games.index') }}"
                    class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all no-underline {{ $isGamesActive ? 'bg-white dark:bg-slate-800 text-sky-600 dark:text-sky-400 shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white/50 dark:hover:bg-slate-800/50' }}">
                    <i class="fa-solid fa-calendar-days text-xs"></i>
                    <span>Schedule</span>
                </a>

                <a href="{{ route('profile') }}"
                    class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all no-underline {{ $isProfileActive ? 'bg-white dark:bg-slate-800 text-sky-600 dark:text-sky-400 shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-white/50 dark:hover:bg-slate-800/50' }}">
                    <i class="fa-solid fa-user-shield text-xs"></i>
                    <span>Locker Room</span>
                </a>

                @if(auth()->user()->hasRole('admin'))
                    <div class="h-4 w-px bg-slate-300 dark:bg-slate-700 mx-1"></div>

                    <a href="{{ route('user_list') }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold transition-all no-underline {{ request()->routeIs('user_list*') ? 'bg-amber-500/20 text-amber-700 dark:text-amber-300' : 'text-slate-600 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-300' }}">
                        <i class="fa-solid fa-users text-xs"></i>
                        <span>Players</span>
                    </a>

                    <a href="{{ route('settings.index') }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold transition-all no-underline {{ request()->routeIs('settings.*') ? 'bg-amber-500/20 text-amber-700 dark:text-amber-300' : 'text-slate-600 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-300' }}">
                        <i class="fa-solid fa-gear text-xs"></i>
                        <span>Settings</span>
                    </a>
                @endif
            @else
                <a href="/" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white no-underline">Home</a>
                <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white no-underline">Schedule</a>
            @endif
        </nav>

        <!-- Right Utilities & Profile Controls -->
        <div class="flex items-center gap-2.5 sm:gap-3 flex-shrink-0">

            @guest
                <a href="{{ route('login') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white text-xs font-bold transition no-underline shadow-sm">
                    Sign In
                </a>
                <a href="{{ route('login') }}?form=register" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-sky-500 to-cyan-400 hover:from-sky-400 hover:to-cyan-300 text-slate-950 font-black text-xs transition no-underline shadow-md shadow-sky-500/20">
                    Register
                </a>
            @else
                <!-- Player Capsule Dropdown with Alpine.js -->
                <div class="relative" x-data="{ userMenuOpen: false }" @click.outside="userMenuOpen = false" @keydown.escape.window="userMenuOpen = false">
                    <button type="button" @click="userMenuOpen = !userMenuOpen"
                        class="flex items-center gap-2 p-1 pl-1.5 pr-2.5 rounded-full border border-slate-200 dark:border-slate-800 bg-slate-100/80 dark:bg-slate-900/80 hover:bg-slate-200/80 dark:hover:bg-slate-800/80 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                        
                        <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-sky-500 to-cyan-400 text-slate-950 font-black text-xs flex items-center justify-center shadow-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>

                        <div class="flex flex-col text-left hidden sm:flex">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate max-w-[110px] md:max-w-[140px] leading-tight">
                                {{ Auth::user()->name }}
                            </span>
                            @if(Auth::user()->jersey_number)
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">#{{ Auth::user()->jersey_number }}</span>
                            @endif
                        </div>

                        @if(Auth::user()->hasRole('admin'))
                            <span class="text-[9px] uppercase font-mono font-bold tracking-wider px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30 hidden md:inline">Admin</span>
                        @endif

                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-0.5 transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="userMenuOpen" x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-56 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl dark:shadow-2xl z-50 p-1.5 divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                        
                        <div class="px-3 py-2">
                            <p class="font-bold text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->email }}</p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition no-underline">
                                <i class="fa-solid fa-user-shield text-sky-500 w-4 text-center"></i>
                                <span>Player Profile &amp; Jersey</span>
                            </a>
                            <a href="{{ route('games.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition no-underline">
                                <i class="fa-solid fa-calendar-days text-sky-500 w-4 text-center"></i>
                                <span>Season Schedule</span>
                            </a>
                        </div>

                        @if(Auth::user()->hasRole('admin'))
                            <div class="py-1">
                                <a href="{{ route('user_list') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-amber-700 dark:text-amber-300 hover:bg-amber-500/10 transition no-underline">
                                    <i class="fa-solid fa-users text-amber-500 w-4 text-center"></i>
                                    <span>Players Directory</span>
                                </a>
                                <a href="{{ route('settings.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-amber-700 dark:text-amber-300 hover:bg-amber-500/10 transition no-underline">
                                    <i class="fa-solid fa-gear text-amber-500 w-4 text-center"></i>
                                    <span>League Settings</span>
                                </a>
                            </div>
                        @endif

                        <div class="pt-1">
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-rose-600 dark:text-rose-400 hover:bg-rose-500/10 transition text-left">
                                    <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i>
                                    <span>Sign Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endguest
        </div>
    </div>
</header>