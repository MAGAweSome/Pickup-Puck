<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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

<body class="min-h-screen bg-deep-navy text-ice antialiased font-sans m-0">

    @include('inc.nav')

    <div class="flex">
        @include('components.sidebar')

        <main class="flex-1 p-6 md:p-10 app-container mx-auto w-full bg-deep-navy min-h-[calc(100vh-4rem)]">
            @yield('content')
        </main>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js"></script>
    @stack('scripts')
</body>

</html>