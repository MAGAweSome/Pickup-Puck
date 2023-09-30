@extends('layouts.app')

@section('content')

<!-- <div class="w-75 bg-light h-100"> -->
    <div>
        <h1 class="text-center">
            {{ $user->name }}'@if(substr($user->name, -1) != "s")s @endif Profile
        </h1>
        
        <form action="" method="POST">
            @csrf

            @if(Session::has('success'))
                <div class="alert alert-success">
                    {{ Session::get('success') }}
                </div>
            @endif

            <div class="form-group">
                <label for="name"><strong>Name:</strong></label>
                <input type="text" class="form-control" id ="name" name="name" value="{{$user->name}}">

                @error('name')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mt-2">
                <label for="email"><strong>Email:</strong></label>
                <input type="text" class="form-control" id ="email" value="{{$user->email}}" name="email">

                @error('email')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="dropdown mt-2">
                <label for="gameRole"><strong>Game Role:</strong></label>
                <select class="form-select" aria-label="Default select example" name="gameRole" id="gameRole">
                    <option value="" selected disabled hidden>{{Str::title($user->role_preference)}}</option>
                    @foreach ($GAME_ROLES as $gamerole)
                        <option value="{{ $gamerole }}">{{ $gamerole->name }}</option>
                    @endforeach
                </select>

                @error('gameRole')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check mt-2">
                <!-- @if ($user->hasRole('admin'))
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" checked>
                @else
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                @endif -->
                <input class="form-check-input" type="checkbox" value="1" id="adminCheck" name="adminCheck" 
                @if ($user->hasRole('admin')) checked @endif>
                

                <label class="form-check-label" for="flexCheckDefault">
                    Admin
                </label>
            </div>

            <button class="btn btn-primary mt-2" type="submit">Update Profile</button>
        </form>
    
    </div>

<!-- </div> -->
@endsection
