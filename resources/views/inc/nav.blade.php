<nav class="navbar navbar-expand-lg row m-0 fixed-top navbar-light bg-light d-flex justify-content-center w-100">
    <div class="w-75 p-0">
        
        @guest
            <a class="navbar-brand float-end me-2 fs-4" href="/login">Login / Register</a>
        @else
            <a class="navbar-brand float-start ms-3-sm fs-4" href="/">Dashboard</a>
            <div class="dropdown float-end" id="navDropdown">
                <a class="navbar-brand fs-4 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{{ Auth::user()->name }}}
                </a>
                <div class="dropdown-menu mt-2 dropdown-menu-end">
                    {{-- <a class="dropdown-item" href="/home">Dashboard</a> --}}
                    <a class="dropdown-item" href="/profile" id="navDropdownProfile">Profile</a>
                    @role ('admin')
                    <a class="dropdown-item" href="/admin/user">Player List</a>
                    @endrole
                    <button class="dropdown-item" id="start-tour">Start Tour</button>
                    <div class="dropdown-divider"></div>
                    <button class="dropdown-item" type="button" onclick="document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </button>
                </div>
            </div>
        @endguest
    </div>
</nav>