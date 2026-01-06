<aside class="hidden md:block w-64 bg-slate-800 text-ice min-h-[calc(100vh-4rem)] p-4">
    <div class="mb-6 px-3">
        <h2 class="text-2xl font-semibold text-ice-blue">Pickup Puck</h2>
        <p class="text-sm text-slate-300">Season · Games · Players</p>
    </div>

    <nav class="space-y-2 px-2 pt-4 border-t border-slate-700">
        <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-700 text-slate-100 border-transparent shadow-none ring-0">
            <i class="fa-solid fa-house text-ice-blue w-4"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('games.index') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-700 text-slate-100 border-transparent shadow-none ring-0">
            <i class="fa-solid fa-calendar-days text-ice-blue w-4"></i>
            <span>Games</span>
        </a>
        <a href="{{ route('user_list') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-700 text-slate-100 border-transparent shadow-none ring-0">
            <i class="fa-solid fa-users text-ice-blue w-4"></i>
            <span>Players</span>
        </a>
        <a href="{{ route('payments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-700 text-slate-100 border-transparent shadow-none ring-0">
            <i class="fa-solid fa-credit-card text-ice-blue w-4"></i>
            <span>Payments</span>
        </a>
        <a href="{{ route('seasons.index') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-700 text-slate-100 border-transparent shadow-none ring-0">
            <i class="fa-solid fa-hourglass-half text-ice-blue w-4"></i>
            <span>Seasons</span>
        </a>
        <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-700 text-slate-100 border-transparent shadow-none ring-0">
            <i class="fa-solid fa-gear text-ice-blue w-4"></i>
            <span>Settings</span>
        </a>
    </nav>
</aside>
