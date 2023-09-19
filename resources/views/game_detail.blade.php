@extends('layouts.app')

@section('content')

<!-- <div class="w-75 bg-light h-100"> -->
    <div class="m-5">
        @if(Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif

        <h1 id="game_details_top" class="text-center">
            {{$game->title}} Details
        </h1>

        @role ('admin')
            <div class=" d-flex flex-row mx-5">
                <div class="p-0 align-self-end">

                    @if($game->ice_cost - $game->collected_game_cost < 0)
                        <h3 class="m-0 text-success">Game Covered: ${{$game->collected_game_cost - $game->ice_cost}} Excess</h3>
                    @else
                        <h3 class="m-0">Still Need: ${{$game->ice_cost - $game->collected_game_cost}}</h3>
                    @endif

                </div>
                <div class="p-0 align-self-end ms-auto">
                    <p class="m-0">Game Price: ${{$game->ice_cost}}</p>
                </div>
            </div>
            
            <div class="progress m-5 mt-0" role="progressbar">
                <div class="progress-bar" style="width: {{ $current_game_price_percentage }}%;">${{$game->collected_game_cost}}</div>
            </div>
        @endrole

        <div style="width: 100%">
            <iframe 
                width="100%" 
                height="600" 
                frameborder="0" 
                crolling="no"
                marginheight="0" 
                marginwidth="0" 
                src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q={{$game->location}}&amp;z=14&amp;output=embed">
            </iframe>
        </div>

        <table class="table table-hover">
            <tbody>
                <th>Title</th>
                <td>{{$game->title}}</td>
            </tbody>
            <!-- <tbody>
                <th>Description</th>
                <td>{{$game->description}}</td>
            </tbody> -->
            <tbody>
                <th>Time</th>
                <td>{{$game->game_time}}</td>
            </tbody>
            <tbody>
                <th>Location</th>
                <td>{{$game->location}}</td>
            </tbody>
            <tbody>
                <th>Duration</th>
                <td>{{$game->duration}} min</td>
            </tbody>
            <!-- <tbody>
                <th>Players</th>
                <td>{{$game->players->count()}}</td>
            </tbody>
            <tbody>
                <th>Goalies</th>
                <td>{{$game->goalies->count()}}</td>
            </tbody> -->
            <tbody>
                <th>Game Price</th>
                <td>${{$game->price}}</td>
            </tbody>
            @role ('admin')
                <tbody>
                    <th>Ice Cost</th>
                    <td>${{$game->ice_cost}}</td>
                </tbody>
            @endrole
        </table>

        @if($user_registered == false)
            <h1 id="acceptGame" class="mt-5">Accept Game</h1>
            <form action="{{ route('game_detail_update.game_id', ['game' => $game->id]) }}" method="POST">
                @csrf
                
                <div class="d-flex dropdown justify-content-center">
                    <div class="input-group w-auto m-5">
                        <select required class="form-select" aria-label="Default select example" name="gameRole" id="gameRole">
                            <option value="" selected disabled hidden>Please Select</option>
                            @foreach ($GAME_ROLES as $gamerole)
                                
                                @if ($gamerole == App\Enums\Games\GameRoles::tryFrom(Auth::user()->role_preference))
                                    <option value="{{ $gamerole }}" selected>{{ $gamerole->name }}</option>
                                @else
                                    <option value="{{ $gamerole }}">{{ $gamerole->name }}</option>
                                @endif

                            @endforeach
                        </select>
                        <button class="btn btn-primary" type="submit" id="accept_game_submit_button" name="game" data-mdb-ripple-color="dark">Accept Game</button>
                    </div>
                </div>
            </form>
        @endif

        {{-- @error('feild') --}}
        <h1 id="attendingGuests">Any Guests Attending</h1>
        <form action="{{ route('game_detail_update_guest.game_id', ['game' => $game->id]) }}" method="POST">
            @csrf
            
            <div class="d-flex dropdown justify-content-center">
                <div class="input-group w-auto m-5">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01">Guest Name:</label>
                    </div>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Full Name" aria-label="Full Name" aria-describedby="basic-addon2" required>
                    @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <select required class="form-select" aria-label="Default select example" name="gameRole" id="gameRole">
                        <option value="" selected disabled hidden>Please Select</option>
                        @foreach ($GAME_ROLES as $gamerole)
                            
                            @if ($gamerole == App\Enums\Games\GameRoles::tryFrom(Auth::user()->role_preference))
                                <option value="{{ $gamerole }}" selected>{{ $gamerole->name }}</option>
                            @else
                                <option value="{{ $gamerole }}">{{ $gamerole->name }}</option>
                            @endif

                        @endforeach
                    </select>
                    <button class="btn btn-primary" type="submit" id="accept_game_submit_button" name="game" data-mdb-ripple-color="dark">Accept Game</button>
                </div>
            </div>
        </form>
        {{-- @enderror --}}

        @if(!$user_is_a_goalie and $user_paid == false)
            <h1 id="pleasePay" class="mt-5">Please Pay</h1>
        
            <form action="{{ route('game_detail_pay.game_id', ['game' => $game->id]) }}" method="POST">
                @csrf
                <div class="input-group mb-3">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" placeholder="15" size="1" value="{{$game->price}}" name="gamePayment" min="{{$game->price}}" required>
                    <select class="form-select" aria-label="Default select example" required>
                        <option value="e-Transfer" selected>e-Transfer</option>
                        <option value="cash">Cash</option>
                    </select>
                    <button type="submit" class="btn btn-primary" name="payment" data-mdb-ripple-color="dark">Submit</button>
                </div>
            </form>
        @endif

        <h1 id="skaters" class="mt-5">Skaters</h1>

        <div class="row">
            <div class="table-responsive col-md-6">
                
                <table class="table table-hover">
                    <thead>
                        <th colspan="3">Players</th>
                    </thead>

                    <tbody>
                        @foreach($players as $player_id => $player_name)

                            <tr>
                                <td class="align-middle">{{$player_name}}</td>

                                @role('admin')
                                    @if ($game->gamePayments()->wherePivot('user_id', $player_id)->exists())
                                        <td class="text-end">Paid: {{ $game->gamePayments()->wherePivot('user_id', $player_id)->pluck('payment')->first() }}</td>
                                        <td class="text-end">Method: {{ $game->gamePayments()->wherePivot('user_id', $player_id)->pluck('method')->first() }}</td>
                                    @else
                                        <td class="text-end" colspan="2"><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paidGame">Enter Payment</button></td>

                                        <!-- Vertically centered modal -->
                                        <div class="modal fade" id="paidGame" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Please enter the ammount paid:</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('admin_game_detail_pay.game_id.player_id', ['game' => $game->id, 'player_id' => $player_id]) }}" method="POST">
                                                            @csrf
                                                            <div class="input-group mb-3">
                                                                <span class="input-group-text">$</span>
                                                                <input type="number" class="form-control" placeholder="15" size="1" value="{{$game->price}}" name="gamePayment" min="{{$game->price}}" required>
                                                                
                                                                <select class="form-select" aria-label="Default select example" name="paymentMethod">
                                                                    <option selected value="e-Transfer">e-Transfer</option>
                                                                    <option value="Cash">Cash</option>
                                                                </select>

                                                                <button type="submit" class="btn btn-primary" name="payment" data-mdb-ripple-color="dark">Submit</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    @endif
                                @endrole
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="table-responsive col-md-6">
                <table class="table table-hover">
                    <thead>
                        <th colspan="2">Goalies</th>
                    </thead>

                    <tbody>
                        @foreach($goalies as $goalie)
                            <tr>
                                <td class="align-middle">{{$goalie}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @role ('admin')
            <h1 id="skaterForGame" class="mt-5">Players Not Yet Signed Up</h1>
        
            <div class="table-responsive">
                @if( $users->count() != count($players_attending))
                    <table class="table table-hover">
                        <thead>
                            <th colspan="3">Players</th>
                        </thead>

                        <tbody>
                            @foreach($users as $user)
                                @if(!in_array($user->name, $players_attending))
                                    <tr>
                                        <td class="align-middle">{{ $user->name }}</td>
                                        <!-- <td>{{ $game->gamePayments()->wherePivot('user_id', $user->id)->pluck('payment')->first() }}</td> -->
                                        <td class="text-end"><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adminAcceptGame">Going <i class="fa-solid fa-check"></i></button></td>

                                        <!-- Vertically centered modal -->
                                        <div class="modal fade" id="adminAcceptGame" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Please submit the player position:</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('admin_game_detail_update.game_id.user_id', ['game' => $game->id, 'user_id' => $user->id]) }}" method="POST">
                                                            @csrf
                                                            
                                                            <div class="d-flex dropdown justify-content-center">
                                                                <div class="input-group w-auto">
                                                                    <select required class="form-select" aria-label="Default select example" name="gameRole" id="gameRole">
                                                                        <option value="" selected disabled hidden>Please Select</option>
                                                                        
                                                                        @foreach ($GAME_ROLES as $gamerole)

                                                                            @if ($gamerole == App\Enums\Games\GameRoles::tryFrom($user->role_preference))
                                                                                <option value="{{ $gamerole }}" selected>{{ $gamerole->name }}</option>
                                                                            @else
                                                                                <option value="{{ $gamerole }}">{{ $gamerole->name }}</option>
                                                                            @endif

                                                                        @endforeach
                                                                    </select>
                                                                    <button class="btn btn-primary" type="submit" id="accept_game_submit_button" name="game" data-mdb-ripple-color="dark">Accept Game</button>
                                                                </div>
                                                            </div>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-secondary" role="alert">
                        <h5 class="m-0">All players are signed up for this game!</h5>
                    </div>
                @endif
            </div>
        @endrole

        <h1 id="teams" class="mt-5">Teams</h1>

        <div class="row">
            <div class="table-responsive col-md-6">
                
                <table class="table table-hover">
                    <thead>
                        <th colspan="3">Light Team</th>
                    </thead>

                    <tbody>
                        @foreach($lightTeamPlayers as $lightTeamPlayer)
                            <tr>
                                <td class="align-middle">{{$lightTeamPlayer}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="table-responsive col-md-6">
                <table class="table table-hover">
                    <thead>
                        <th colspan="2">Dark Team</th>
                    </thead>

                    <tbody>
                        
                        @foreach($darkTeamPlayers as $darkTeamPlayer)
                            <tr>
                                <td class="align-middle">{{$darkTeamPlayer}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($currentTime < $game->time->subMinutes(30))
                <div class="alert alert-secondary" role="alert">
                    <h5 class="m-0">Teams are made 30 minutes prior to start of game!</h5>
                </div>
            @endif
        </div>

    </div>

<!-- </div> -->
@endsection
