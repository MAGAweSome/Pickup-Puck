@php
    $isHomeActive = request()->routeIs('home');
    $isGamesActive = request()->routeIs('games.*') || request()->routeIs('game_detail.*') || request()->is('game/*');
    $isProfileActive = request()->routeIs('profile*');
    $isAdminActive = request()->routeIs('user_list*') || request()->routeIs('settings.*');
@endphp

<div class="fixed bottom-0 left-0 right-0 z-40 lg:hidden bg-white/95 dark:bg-slate-950/95 backdrop-blur-xl border-t border-slate-200 dark:border-slate-800 shadow-[0_-4px_20px_rgba(0,0,0,0.08)] dark:shadow-[0_-4px_20px_rgba(0,0,0,0.5)] transition-colors">
    <div class="max-w-md mx-auto px-4 h-16 flex items-center justify-around">
        <!-- Home / Dashboard -->
        <a href="{{ auth()->check() ? route('home') : '/' }}"
            class="flex flex-col items-center justify-center py-1 px-2.5 rounded-xl transition-all duration-150 {{ $isHomeActive ? 'text-sky-600 dark:text-sky-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            <i class="fa-solid fa-house text-lg mb-0.5"></i>
            <span class="text-[10px] tracking-tight">Home</span>
        </a>

        <!-- Games Schedule -->
        <a href="{{ route('games.index') }}"
            class="flex flex-col items-center justify-center py-1 px-2.5 rounded-xl transition-all duration-150 {{ $isGamesActive ? 'text-sky-600 dark:text-sky-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            <i class="fa-solid fa-calendar-days text-lg mb-0.5"></i>
            <span class="text-[10px] tracking-tight">Schedule</span>
        </a>

        @if(auth()->check())
            <!-- Locker Room / Profile -->
            <a href="{{ route('profile') }}"
                class="flex flex-col items-center justify-center py-1 px-2.5 rounded-xl transition-all duration-150 {{ $isProfileActive ? 'text-sky-600 dark:text-sky-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <i class="fa-solid fa-user-shield text-lg mb-0.5"></i>
                <span class="text-[10px] tracking-tight">Locker Room</span>
            </a>

            @if(auth()->user()->hasRole('admin'))
                <!-- Admin Hub -->
                <a href="{{ route('user_list') }}"
                    class="flex flex-col items-center justify-center py-1 px-2.5 rounded-xl transition-all duration-150 {{ $isAdminActive ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="fa-solid fa-shield-halved text-lg mb-0.5"></i>
                    <span class="text-[10px] tracking-tight">Admin</span>
                </a>
            @endif
        @else
            <!-- Sign In -->
            <a href="{{ route('login') }}"
                class="flex flex-col items-center justify-center py-1 px-2.5 rounded-xl transition-all duration-150 text-sky-600 dark:text-sky-400 font-bold">
                <i class="fa-solid fa-right-to-bracket text-lg mb-0.5"></i>
                <span class="text-[10px] tracking-tight">Sign In</span>
            </a>
        @endif
    </div>
</div>
