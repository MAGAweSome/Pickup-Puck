@extends('layouts.app')

@section('content')

<!-- <div class="w-75 bg-light h-100"> -->
    <div>
        <h1 class="text-center" data-title="Hello {{Auth::user()->name}}!" data-intro="Let me show you around!">
            Welcome {{{ Auth::user()->name }}} 
            <!-- You Are Logged In As
            @role ('admin')
                Admin
            @else
                User
            @endrole -->
        </h1>

        <h3>Upcoming Games:</h3>

        @php
            $upcomingGamesExist = false;
        @endphp

        @foreach ($games as $game)
            @if ($game->time > $currentTime)
                @php
                    $upcomingGamesExist = true;
                @endphp
                
                <div class="card mb-2" id="gameCard">
                    <div class="card-header row align-items-center justify-content-between m-0">
                        <!-- <h5 class="col-2 m-0"><i class="fa-regular fa-calendar fa-2xl" style="color: #005eff;"></i></h5> -->
                        <h5 class="col-12 m-0">{{$game->title}} | {{$game->game_time}}</h5>
                        
                        @if(in_array($game->id, $gamesAttending))
                            <h5 class="col-auto m-0 text-success">Attending</h5>
                        @else
                            <h5 class="col-auto m-0 text-danger">Not Yet Attending</h5>
                        @endif

                        <h5 class="col-auto m-0">Game Cost: ${{$game->price}}</h5>
                        
                        @role ('admin')
                            <a class="col-auto m-0 text-decoration-none text-dark" href="/admin/edit_game/{{$game->id}}"><h5 class="m-0"><b>Edit <!--<i class="fas fa-edit"></i>--></b></h5></a>
                        @endrole
                    </div>
                    <div class="card-body row align-items-center justify-content-between m-0" id="gameLocation_Players">
                        <a href="https://maps.google.com/?q={{$game->location}}" target="_blank" class="col-auto text-decoration-none text-dark m-0"><h5 class="card-title m-0"><i class="fa-solid fa-location-dot"></i> {{$game->location}}</h5></a>
                        <h5 class="col-auto m-0">{{$game->players->count()}} Players | {{$game->goalies->count()}} Goalies</h5>
                        <a id="gameMoreDetails" href="game/{{$game->id}}" class="col-auto btn btn-primary m-0" data-title="View Game Details" data-intro="Click here to accept the game and see more details about the game.">See more!</a>
                    </div>
                </div>

            @endif
        @endforeach

        @if (!$upcomingGamesExist)
            <div class="alert alert-secondary" role="alert">
                <h5 class="m-0">There Are No Upcoming Games... Yet!</h5>
            </div>
        @endif

        @if ($hasNotSignedUpForAllGames)
            <a href="{{ route('seasons.accept-all', ['season' => $game->season_id]) }}" class="btn btn-primary w-100 mb-2">Accept All Games in This Season</a>
        @endif

        @role ('admin')
            <a class="btn btn-primary w-100" href="/admin/create_game">Create new game!</a>
        @endrole

        <h3 class="mt-4">Previous Games:</h3>
        
        @php
            $passedGamesExist = false;
        @endphp

        @foreach ($games as $game)
            @if ($game->time < $currentTime)
                @php
                    $passedGamesExist = true;
                @endphp
                <div class="card mb-2">
                    <div class="card-header row align-items-center justify-content-between m-0">
                        <!-- <h5 class="col-2 m-0"><i class="fa-regular fa-calendar fa-2xl" style="color: #005eff;"></i></h5> -->
                        <h5 class="col-sm-9 col-12 m-0">{{$game->title}} | {{$game->game_time}}</h5>
                        <h5 class="col-auto m-0">Game Cost: ${{$game->price}}</h5>
                    </div>
                    <div class="card-body row align-items-center justify-content-between m-0">
                        <h5 class="col-auto card-title m-0"><i class="fa-solid fa-location-dot"></i> {{$game->location}}</h5>
                        <h5 class="col-auto m-0">{{$game->players->count()}} Players | {{$game->goalies->count()}} Goalies</h5>
                        {{-- @role('admin')
                            <h5 class="col-auto m-0">${{$game->collected_game_cost}} of ${{$game->ice_cost}} | Collected for Game</h5>
                        @endrole --}}
                    </div>
                </div>
            @endif
        @endforeach

        @if (!$passedGamesExist)
            <div class="alert alert-secondary" role="alert">
                <h5 class="m-0">There Are No Previous Games!</h5>
            </div>
        @endif
        
    </div>

<!-- </div> -->
@endsection
