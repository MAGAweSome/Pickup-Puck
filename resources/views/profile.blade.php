@extends('layouts.app')

@section('content')

<!-- <div class="w-75 bg-light h-100"> -->
    <div>
        <h1 class="text-center">Account Information</h1>

        @csrf

        <div class="container">
            <div class="row align-items-center justify-content-between">
                <div class="col-md-6"><h4>Name: {{ Auth::user()->name }}</h4></div>
                <div class="col-md-6"><h4>e-Mail: {{ Auth::user()->email }}</h4></div>
                <div class="col-md-6"><h4>Name Role: {{ ucfirst(trans(Auth::user()->role_preference)) }}</h4></div>
                <a href="{{ route('update_profile') }}" class="col-6 text-decoration-none text-dark"><h4><i class="fa-regular fa-pen-to-square"></i> Update Profile</h4></a>
            </div>
        </div>

    </div>

<!-- </div> -->
@endsection