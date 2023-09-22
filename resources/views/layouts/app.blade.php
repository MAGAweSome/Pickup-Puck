<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Pickup Puck</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        @vite(['resources/scss/app.scss', 'resources/js/app.js'])

        <!-- Font Awesome -->
        <script src="https://kit.fontawesome.com/2a47ed06c2.js" crossorigin="anonymous"></script>

        <!-- Make site light/dark to OS default -->
        <!-- <meta name="color-scheme" content="light dark">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-dark-5@1.1.3/dist/css/bootstrap-dark.min.css" rel="stylesheet">
        <meta name="theme-color" content="#111111" media="(prefers-color-scheme: light)">
        <meta name="theme-color" content="#eeeeee" media="(prefers-color-scheme: dark)"> -->

        {{-- For Guest Player Search --}}
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>

    <body class="antialiased siding justify-content-center row m-0 d-flex"> 

        @include('inc.nav')
    
        <div class="container">
            <div class="row align-items-start h-100">
                <div class="col-md-1 d-md-inline d-sm-none"></div>
                <div class="col-md-10 bg-light h-100">@yield('content')</div>
                <div class="col-md-1 d-md-inline d-sm-none"></div>
            </div>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </body>

</html>
