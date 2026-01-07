<header class="fixed top-0 left-0 right-0 bg-slate-800 z-50 relative h-16">
    <div class="absolute left-4 inset-y-0 flex items-center">
        <a href="/" class="flex items-center gap-3">
            <img src="{{ asset('photos/Pickup Puck Logo.png') }}" alt="Pickup Puck Logo" class="h-10 ml-4 w-auto">
            <!-- <span class="text-ice-blue font-semibold text-lg ml-2 hidden md:inline">Pickup Puck</span> -->
            <span class="text-sm text-slate-300 hidden md:inline">— Pickup hockey scheduling made simple</span>
        </a>
    </div>

    <div class="app-container mx-auto px-6 flex items-center justify-end h-16">
        <div class="flex items-center gap-4">
            @guest
                <a href="/login" class="px-3 py-1 rounded bg-ice-blue text-deep-navy font-medium hover:text-deep-navy hover:bg-ice-blue">Login / Register</a>
            @else
                <a href="/profile" class="text-slate-200">{{ Auth::user()->name }}</a>
                @if(auth()->check() && auth()->user()->hasRole('admin'))
                    <a href="/admin/user" class="ml-3 text-slate-200">Players</a>
                @endif
                <button onclick="document.getElementById('logout-form').submit();" class="ml-3 px-3 py-1 rounded border border-slate-700 text-slate-200">Logout</button>
            @endguest
        </div>
    </div>
</header>