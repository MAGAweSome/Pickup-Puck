@extends('layouts.app')

@section('content')

    <div>
        <h1 class="text-2xl font-semibold text-ice mb-4">Player List</h1>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach($users as $user)
                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <a href="/admin/user/{{$user->id}}" class="text-ice text-lg font-medium">{{ $user->name }}</a>
                            <div class="text-sm text-slate-300">{{ $user->email }}</div>
                        </div>
                        <div class="text-right text-sm">
                            <div class="text-slate-300">{{ Str::title($user->role_preference) }}</div>
                            @if ($user->hasRole('admin'))
                                <div class="mt-1 text-xs bg-amber-600 text-amber-900 px-2 py-0.5 rounded">Admin</div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <a class="w-full text-center px-3 py-2 bg-ice-blue text-deep-navy hover:text-deep-navy rounded" href="/admin/user/{{$user->id}}/history">Game History</a>
                        <a class="w-full text-center px-3 py-2 border border-slate-600 hover:text-white rounded" href="/admin/user/{{$user->id}}">View</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-10">
            <h2 class="text-2xl font-semibold text-ice mb-4">Guest List</h2>

            @if(!isset($guests) || $guests->isEmpty())
                <div class="p-4 rounded bg-slate-700 text-slate-200">There are no guests yet.</div>
            @else
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($guests as $guest)
                        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-ice text-lg font-medium">{{ $guest->name }}</div>
                                    <div class="text-sm text-slate-300">Guest #{{ $guest->id }}</div>
                                </div>
                            </div>

                            <div class="mt-4 flex gap-2">
                                <a class="w-full text-center px-3 py-2 bg-ice-blue text-deep-navy hover:text-deep-navy rounded" href="{{ route('guest_game_history', ['guest' => $guest->id]) }}">Game History</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

@endsection
