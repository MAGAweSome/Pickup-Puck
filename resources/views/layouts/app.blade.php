<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pickup Puck</title>

    <!-- Inter font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CDN (for rapid UI overhaul). Keep @@vite for existing assets. -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'deep-navy': '#051426',
                        'slate-gray': '#2b3944',
                        'ice-blue': '#a7e9ff',
                        'ice': '#f2fbff'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/2a47ed06c2.js" crossorigin="anonymous"></script>

    <!-- Utilities kept from original layout -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/introjs.min.css" />

    <style>
        /* Small utility for container max width matching previous layout */
        .app-container { max-width: 1100px; }
        /* Ensure only the top margin above the header is removed without touching page content spacing */
        html, body { margin: 0 !important; padding: 0 !important; background-color: #051426; }
        header { margin: 0 !important; }
        /* navbar height constant used to offset main layout */
        :root { --nav-height: 4rem; }
    </style>
</head>

<body class="min-h-screen bg-deep-navy text-ice antialiased font-sans m-0 overflow-hidden" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">

    @include('inc.nav')

    <!-- Mobile/Tablet Sidebar Drawer -->
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-50 lg:hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/60" @click="sidebarOpen = false"></div>
        <aside class="absolute left-0 top-[var(--nav-height)] h-[calc(100dvh-var(--nav-height))] w-72 max-w-[85vw] bg-slate-800 text-ice border-r border-slate-700 overflow-y-auto p-4"
              @click.stop
              x-transition:enter="transition ease-out duration-200"
              x-transition:enter-start="-translate-x-full"
              x-transition:enter-end="translate-x-0"
              x-transition:leave="transition ease-in duration-200"
              x-transition:leave-start="translate-x-0"
              x-transition:leave-end="-translate-x-full">
            <div class="flex items-center justify-between mb-3">
                <div class="text-sm font-semibold text-slate-200">Menu</div>
                <button type="button" class="p-2 rounded hover:bg-slate-700" @click="sidebarOpen = false" aria-label="Close menu">
                    <svg class="h-5 w-5 text-slate-200" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div @click="sidebarOpen = false">
                @include('components.sidebar_content')
            </div>
        </aside>
    </div>

    <div class="flex h-[calc(100dvh-var(--nav-height))] mt-[var(--nav-height)]">
        @include('components.sidebar')

        <main class="flex-1 overflow-y-auto bg-deep-navy">
            <div class="p-6 md:p-10 app-container mx-auto w-full">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js"></script>
    @stack('scripts')
</body>

</html>