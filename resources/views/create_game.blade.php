@extends('layouts.app')

@section('content')

<!-- <div class="w-75 bg-light h-100"> -->
    <div class="m-5">
        <h1 class="text-center">
            Please fill out the game data below
        </h1>
        
        <hr>
        <form  action="" method="POST" class="needs-validation">
            
            @csrf

            <div class="row align-items-center justify-content-between m-0">
                <div class="col-lg-2">
                    <h5>Title: <input id="title" name="title" type="text" class="form-control" placeholder="Game 1" required></h5>
                </div>
                <div class="col-lg-3">
                    <div class="cs-form">
                        <h5>Date: <input id="date" name="date" type="date" class="form-control" max="9999-12-31" required></h5>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="cs-form">
                        <h5>Time: <input id="time" name="time" type="time" class="form-control" required></h5>
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
                            <input id="price" name="price" type="text" class="form-control" placeholder="15" size="1" required>
                        </div>
                    </h5>
                </div>
                <div class="col-lg-3">
                    <h5>Cost pof Ice: 
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input id="ice_cost" name="ice_cost" type="text" class="form-control" placeholder="200" size="1" required>
                        </div>
                    </h5>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Add Game</button>
        </form>
        
    </div>

<!-- </div> -->
@endsection
