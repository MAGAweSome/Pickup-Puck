@extends('layouts.app')

@section('content')
    <!-- <div class="w-75 bg-light h-100"> -->
    <div>
        <h1 class="text-center">
            Please fill out the game data below
        </h1>

        <hr>
        <form action="" method="POST" class="needs-validation" id="createGameForm">

            @csrf

            <div class="row align-items-center justify-content-between m-0">
                <div class="col-lg-2">
                    <h5>Title: <input id="title" name="title" type="text" class="form-control" placeholder="Game 1"
                            required></h5>
                </div>
                <div class="col-lg-3">
                    <div class="cs-form">
                        <h5>Date: <input id="date" name="date" type="date" class="form-control" max="9999-12-31"
                                required></h5>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="cs-form">
                        <h5>Time: <input id="time" name="time" type="time" class="form-control" required></h5>
                    </div>
                </div>
                <div class="col-lg-5">
                    <h5>Location: <input id="location" name="location" type="text" class="form-control"
                            placeholder="1001 Franklin Blvd, Cambridge, ON N1R 8B5"
                            value="1001 Franklin Blvd, Cambridge, ON N1R 8B5" required></h5>
                </div>
                <div class="col-lg-3">
                    <h5>Duration:
                        <div class="input-group">
                            <input id="duration" name="duration" type="text" class="form-control" placeholder="50"
                                value="50" size="1" required>
                            <span class="input-group-text">min</span>
                        </div>
                    </h5>
                </div>
                <div class="col-lg-3">
                    <h5>Price Per Player:
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input id="price" name="price" type="text" class="form-control" placeholder="15"
                                size="1" required>
                        </div>
                    </h5>
                </div>
                <div class="col-lg-3">
                    <div class="cs-form">
                        <h5>Season:
                            <select id="season" name="season" class="form-select" required>
                                @if (count($seasons) == 0)
                                    <optgroup label="Create a Season" disabled></optgroup>
                                @else
                                    <optgroup label="Seasons:" disabled></optgroup>
                                @endif

                                @foreach ($seasons as $season)
                                    <option value="{{ $season->id }}">Season {{ $season->season_number }}</option>
                                @endforeach
                                <optgroup label="Choose a season" style="display: none;">
                                    <option value="" selected>Choose a season</option>
                                </optgroup>
                            </select>
                        </h5>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="cs-form">
                        <button type="button" class="btn btn-primary col-lg-12" data-bs-toggle="modal"
                            data-bs-target="#addNewSeasonModal" data-bs-dismiss="modal">Add New Season</button>
                    </div>
                </div>
                {{-- <div class="col-lg-3">
                    <h5>Cost of Ice: 
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input id="ice_cost" name="ice_cost" type="text" class="form-control" placeholder="200" size="1" required>
                        </div>
                    </h5>
                </div> --}}
                
                <div class="col-lg-12">
                    <div class="cs-form">
                        <button type="submit" class="btn btn-primary col-lg-12 mt-2">Add Game</button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Add New Season Modal -->
        <div class="modal fade" id="addNewSeasonModal" tabindex="-1" role="dialog"
            aria-labelledby="addNewSeasonModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNewSeasonModalLabel">Add New Season</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="addNewSeasonForm" action="{{ route('season.create') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <!-- Form fields for creating a new season -->
                            <div class="mb-3">
                                <label for="season_number" class="form-label">Season Number</label>
                                <input type="number" class="form-control" id="season_number" name="season_number"
                                    min="1" value="{{ $nextSeasonNumber }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Add Season</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>

    <!-- </div> -->
@endsection
