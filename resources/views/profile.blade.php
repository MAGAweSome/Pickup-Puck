@extends('layouts.app')

@section('content')

    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold">Profile</h1>
        <p class="mt-2 text-slate-300">Update your account information and role preferences.</p>

        <div class="mt-6 bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-ice mb-4">Account Information</h2>

            @if(session('message'))
                <div class="mb-3 p-3 rounded bg-emerald-600 text-white">{{ session('message') }}</div>
            @endif

            <form action="{{ route('profile_update') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-slate-300 text-sm">Name</label>
                    <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" class="mt-1 w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice" required minlength="4">
                    @error('name') <div class="text-red-400 text-sm">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-slate-300 text-sm">Email</label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="mt-1 w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice" required>
                    @error('email') <div class="text-red-400 text-sm">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-slate-300 text-sm">Preferred Position</label>
                    <select id="playerDesiredRole" name="role" class="mt-1 w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">
                        <option value="" disabled {{ old('role', Auth::user()->role_preference) ? '' : 'selected' }}>Select Position</option>
                        @foreach (App\Enums\Games\GameRoles::cases() as $roleOption)
                            <option value="{{ $roleOption->value }}" {{ old('role', Auth::user()->role_preference) == $roleOption->value ? 'selected' : '' }}>{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $roleOption->name)) }}</option>
                        @endforeach
                    </select>
                    @error('role') <div class="text-red-400 text-sm">{{ $message }}</div> @enderror
                </div>

                <div class="flex gap-3">
                    <button id="updateProfile" type="submit" class="px-4 py-2 bg-ice-blue text-deep-navy rounded font-medium">Update Profile</button>

                    <button type="button" id="start-tour-btn" class="px-4 py-2 border border-slate-600 rounded text-ice">Start Tour</button>
                </div>
            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function(){
                var btn = document.getElementById('start-tour-btn');
                if (!btn) return;
                btn.addEventListener('click', function(){
                    // Restart onboarding flow from the dashboard
                    window.location.href = '/home?onboarding=1&restart=1';
                });
            });
        </script>
    </div>

@endsection