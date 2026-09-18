@extends('layouts.app')

@section('content')

<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-slate-100">Player Directory</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-400">View roster members, preferences, skill levels, and game history.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-mono font-bold text-slate-700 dark:text-slate-300">
                <i class="fa-solid fa-users text-sky-500"></i>
                <span>{{ $users->count() }} Registered</span>
            </span>
            @if(isset($guests) && $guests->isNotEmpty())
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-mono font-bold text-slate-700 dark:text-slate-300">
                    <i class="fa-solid fa-user-tag text-indigo-500"></i>
                    <span>{{ $guests->count() }} Guests</span>
                </span>
            @endif
        </div>
    </div>

    <!-- Players Grid -->
    <div>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach($users as $user)
                <div class="bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 rounded-2xl p-5 shadow-sm dark:shadow-md hover:border-sky-400 dark:hover:border-sky-500/50 transition duration-150 flex flex-col justify-between group">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <a href="/admin/user/{{$user->id}}" class="text-base font-bold text-slate-900 dark:text-slate-100 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition truncate block no-underline">
                                    {{ $user->name }}
                                </a>
                                <div class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ $user->email }}</div>
                            </div>
                            <div class="text-right shrink-0 flex flex-col items-end gap-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-mono font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    {{ Str::title($user->role_preference ?? 'Any') }}
                                </span>
                                @if ($user->hasRole('admin'))
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30">
                                        <i class="fa-solid fa-shield-halved text-[9px]"></i>
                                        <span>Admin</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 grid grid-cols-2 gap-2">
                        <a class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-sky-50 hover:bg-sky-100 dark:bg-sky-500/15 dark:hover:bg-sky-500/25 border border-sky-200 dark:border-sky-500/30 text-sky-700 dark:text-sky-300 hover:text-sky-900 dark:hover:text-white rounded-xl text-xs font-bold transition no-underline shadow-sm" href="/admin/user/{{$user->id}}/history">
                            <i class="fa-solid fa-clock-rotate-left text-[11px]"></i>
                            <span>History</span>
                        </a>
                        <a class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white rounded-xl text-xs font-bold transition no-underline shadow-sm" href="/admin/user/{{$user->id}}">
                            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                            <span>View / Edit</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Guest List -->
    <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-slate-100">Guest List</h2>
        </div>

        @if(!isset($guests) || $guests->isEmpty())
            <div class="p-8 text-center rounded-2xl bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 text-slate-500 dark:text-slate-400 shadow-sm">
                <i class="fa-regular fa-user text-2xl text-slate-400 dark:text-slate-500 mb-2 block"></i>
                <p class="font-bold text-sm text-slate-800 dark:text-slate-200">There are no guests recorded yet.</p>
            </div>
        @else
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($guests as $guest)
                    <div class="bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 rounded-2xl p-5 shadow-sm dark:shadow-md hover:border-sky-400 dark:hover:border-sky-500/50 transition duration-150 flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="text-base font-bold text-slate-900 dark:text-slate-100 truncate">
                                        {{ $guest->name }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">Guest #{{ $guest->id }}</div>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-mono font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                    Guest
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80">
                            <a class="inline-flex items-center justify-center gap-1.5 w-full px-3 py-2 bg-sky-50 hover:bg-sky-100 dark:bg-sky-500/15 dark:hover:bg-sky-500/25 border border-sky-200 dark:border-sky-500/30 text-sky-700 dark:text-sky-300 hover:text-sky-900 dark:hover:text-white rounded-xl text-xs font-bold transition no-underline shadow-sm" href="{{ route('guest_game_history', ['guest' => $guest->id]) }}">
                                <i class="fa-solid fa-clock-rotate-left text-[11px]"></i>
                                <span>Game History</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
