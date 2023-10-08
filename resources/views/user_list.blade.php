@extends('layouts.app')

@section('content')

<!-- <div class="w-75 bg-light h-100"> -->
    <h1 class="text-center">Player List</h1>
        
    @foreach($users as $user)

        <div class="row align-items-center justify-content-between m-0">
            <div class="col-lg-3">
                <h5><b>Name:</b> <a href="/admin/user/{{$user->id}}" class="text-decoration-none text-dark">{{ $user->name }}</a></h5>
            </div>
            <div class="col-lg-4">
                <h5><b>e-Mail:</b> {{ $user->email }}</h5>
            </div>
            <div class="col-lg-3">
                <h5><b>Prefered Role:</b> {{ Str::title($user->role_preference) }}</h5>
            </div>
            <div class="col-lg-2">
                @if ($user->hasRole('admin'))
                    <h5 class="">Admin</h5>
                @else    
                    <h5 class=""></h5>
                @endif
            </div>
            <div class="col-lg-12 mb-2">
                <a class="btn btn-primary w-100" href="/admin/user/{{$user->id}}/history" role="button">Game History</a>
            </div>
            <div class="col-lg-12 mb-3">
                <a class="btn btn-primary w-100" href="/admin/user/{{$user->id}}" role="button">View</a>
            </div>
            <hr>
            
        </div>

    @endforeach

<!-- </div> -->
@endsection
