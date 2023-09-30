@extends('layouts.app')

@section('content')

<!-- <div class="w-75 bg-light h-100"> -->
    <div>
        <h1 class="text-center">Player List</h1>

        <table class="table table-hover">
            <thead>
                <th>Name</th>
                <th>Email</th>
                <th>Prefered Role</th>
                <th></th>
                <th></th>
                <th></th>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td class="align-middle"><a href="/admin/user/{{$user->id}}" class="text-decoration-none text-dark"><p class="m-0">{{ $user->name }}</p></a></td>
                        <td class="align-middle">{{$user->email}}</td>
                        <td class="align-middle">{{Str::title($user->role_preference)}}</td>
                        @if ($user->hasRole('admin'))
                            <td class="align-middle text-end">Admin</td>
                        @else    
                            <td class="align-middle text-end"></td>
                        @endif
                        <td class="text-end align-middle"><a class="btn btn-primary" href="/admin/user/{{$user->id}}/history" role="button">Game History</a></td>
                        <td class="text-end align-middle"><a class="btn btn-primary" href="/admin/user/{{$user->id}}" role="button">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    
    </div>

<!-- </div> -->
@endsection
