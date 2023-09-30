@extends('layouts.app')

@section('content')

<!-- <div class="w-75 bg-light h-100"> -->
    <div>
        <h1 class="text-center">
            Updating: {{ $game->title }}
        </h1>
        
        <hr>
        <form  action="" method="POST" class="needs-validation">
            
            @csrf

            <div class="row align-items-center justify-content-between m-0">
                <div class="col-lg-2">
                    <h5>Title: <input id="title" name="title" type="text" class="form-control" placeholder="Game 1" value="{{$game->title}}" required></h5>
                </div>
                <div class="col-lg-3">
                    <div class="cs-form">
                        <h5>Date: 
                            <div class="cs-form">
                                <input id="date" name="date" type="date" class="form-control" max="9999-12-31" value="{{$game_date}}" required>
                            </div>
                        </h5>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="cs-form">
                        <h5>Time: 
                            <div class="cs-form">
                                <input id="time" name="time" type="time" class="form-control" value="{{$game_time}}" required>
                            </div>
                        </h5>
                    </div>
                </div>
                <div class="col-lg-5">
                    <h5>Location: <input id="location" name="location" type="text" class="form-control" placeholder="1001 Franklin Blvd, Cambridge, ON N1R 8B5" value="1001 Franklin Blvd, Cambridge, ON N1R 8B5" required></h5>
                </div>
                <div class="col-lg-3">
                    <h5>Duration: 
                        <div class="input-group">
                            <input id="duration" name="duration" type="text" class="form-control" placeholder="50" value="50" size="1" required>
                            <span class="input-group-text">min</span>
                        </div>
                    </h5>
                </div>
                <div class="col-lg-3">
                    <h5>Price Per Player: 
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input id="price" name="price" type="text" class="form-control" placeholder="15" size="1" value="{{$game->price}}" required>
                        </div>
                    </h5>
                </div>
                <div class="col-lg-3">
                    <h5>Cost pof Ice: 
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input id="ice_cost" name="ice_cost" type="text" class="form-control" placeholder="200" size="1" value="{{$game->ice_cost}}" required>
                        </div>
                    </h5>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Game</button>
        </form>
        
    </div>

<!-- </div> -->
@endsection
