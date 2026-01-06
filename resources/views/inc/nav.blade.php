<header class="w-full bg-slate-800 sticky top-0 z-40">
    <div class="app-container mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/" class="text-ice-blue font-semibold text-lg">Pickup Puck</a>
            <span class="text-sm text-slate-300 hidden md:inline">— Pickup hockey scheduling made simple</span>
        </div>

        <div class="flex items-center gap-4">
            @guest
                <a href="/login" class="px-3 py-1 rounded bg-ice-blue text-deep-navy font-medium">Login / Register</a>
            @else
                <a href="/profile" class="text-slate-200">Profile</a>
                @role ('admin')
                    <a href="/admin/user" class="text-slate-200">Players</a>
                @endrole
                <button onclick="document.getElementById('logout-form').submit();" class="ml-2 px-3 py-1 rounded border border-slate-700 text-slate-200">Logout</button>
            @endguest
        </div>
    </div>
</header>