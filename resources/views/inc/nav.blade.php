<header id="navigationBar" class="fixed top-0 left-0 right-0 bg-slate-800 z-50 h-16">
    <div class="h-16 w-full flex items-center justify-between px-4 sm:px-6">
        <div class="flex items-center gap-3 min-w-0">
            <button type="button" class="lg:hidden p-2 rounded hover:bg-slate-700" @click="sidebarOpen = true" aria-label="Open menu">
                <svg class="h-5 w-5 text-slate-200" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
            </button>

            <a href="/" class="flex items-center gap-3 no-underline min-w-0">
                <img src="{{ asset('photos/Pickup Puck Logo.png') }}" alt="Pickup Puck Logo" class="h-9 w-auto">
                <span class="text-sm text-slate-300 hidden sm:inline truncate">Pickup hockey scheduling made simple</span>
            </a>
        </div>

        <div class="flex items-center gap-3 flex-shrink-0">
            @guest
                <a href="/login" class="px-3 py-1 rounded bg-ice-blue text-deep-navy font-medium hover:text-deep-navy hover:bg-ice-blue">Login</a>
            @else
                <span class="text-slate-200 m-0 leading-none hidden sm:inline">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="px-3 py-1 rounded border border-slate-700 text-slate-200">Logout</button>
                </form>
            @endguest
        </div>
    </div>
</header>