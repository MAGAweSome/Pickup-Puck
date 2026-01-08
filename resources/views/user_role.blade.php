@extends('layouts.app')

@section('content')

    <div class="max-w-3xl mx-auto">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white">
                    {{ $user->name }}@if(substr($user->name, -1) != 's')'s @endif Profile
                </h1>
                <p class="mt-2 text-sm text-white/70">
                    Admin view of this player's account settings.
                    <span class="text-white/80">User #{{ $user->id }}</span>
                    · <span class="text-white/80">{{ $user->email }}</span>
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('user_list') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-800/60 ring-1 ring-white/10 px-3 py-2 text-sm font-semibold text-ice hover:text-ice hover:bg-slate-700/60 transition">All Users</a>
                <a href="{{ route('user_game_history', ['user' => $user->id]) }}" class="inline-flex items-center justify-center rounded-lg bg-slate-800/60 ring-1 ring-white/10 px-3 py-2 text-sm font-semibold text-ice hover:text-ice hover:bg-slate-700/60 transition">Game History</a>
            </div>
        </div>

        @if (session('success'))
            <div class="mt-5 rounded-xl border border-green-400/30 bg-green-500/10 px-4 py-3 text-green-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-6 bg-slate-gray/40 border border-white/10 rounded-2xl p-6">
            <h2 class="text-xl font-semibold text-ice mb-4">Account Information</h2>

            <form action="{{ route('user_role_update.user_id', ['user' => $user->id]) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-white">Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        minlength="4"
                        class="mt-2 w-full rounded-xl bg-deep-navy/70 border border-white/10 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-ice-blue/60"
                    />
                    @error('name')
                        <div class="text-red-300 text-sm mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-white">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        class="mt-2 w-full rounded-xl bg-deep-navy/70 border border-white/10 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-ice-blue/60"
                    />
                    @error('email')
                        <div class="text-red-300 text-sm mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="gameRole" class="block text-sm font-semibold text-white">Preferred Position</label>
                    <select
                        id="gameRole"
                        name="gameRole"
                        class="mt-2 w-full rounded-xl bg-deep-navy/70 border border-white/10 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-ice-blue/60"
                    >
                        <option value="" {{ old('gameRole', $user->role_preference) ? '' : 'selected' }}>Select Position</option>
                        @foreach ($GAME_ROLES as $gamerole)
                            <option value="{{ $gamerole->value }}" {{ old('gameRole', $user->role_preference) === $gamerole->value ? 'selected' : '' }}>
                                {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $gamerole->name)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('gameRole')
                        <div class="text-red-300 text-sm mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-3 select-none">
                        <input
                            type="checkbox"
                            value="1"
                            id="adminCheck"
                            name="adminCheck"
                            class="h-5 w-5 rounded border-white/20 bg-deep-navy/70 text-ice-blue focus:ring-ice-blue/60"
                            @if (old('adminCheck', $user->hasRole('admin') ? '1' : null)) checked @endif
                        />
                        <span class="text-sm font-semibold text-white">Admin</span>
                        <span class="text-xs text-white/60">(Grants access to admin pages)</span>
                    </label>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button
                        class="inline-flex items-center justify-center rounded-xl bg-ice-blue text-deep-navy font-bold px-5 py-3 hover:opacity-90 transition"
                        type="submit"
                    >
                        Save Changes
                    </button>

                    <a href="{{ route('user_list') }}" class="text-sm text-white/70 hover:text-white underline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
