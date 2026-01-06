@extends('layouts.app')

@section('content')

<!-- <div class="w-75 bg-light h-100"> -->
    <div class="max-w-2xl mx-auto bg-slate-800 border border-slate-700 rounded-lg p-6">
        <h1 class="text-xl font-semibold text-ice mb-4">Account Information</h1>

        @if(session('message'))
            <div class="mb-3 p-3 rounded bg-green-600 text-white">{{ session('message') }}</div>
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
                <label class="block text-slate-300 text-sm">Preferred Role</label>
                <input type="text" name="role" value="{{ old('role', Auth::user()->role_preference) }}" class="mt-1 w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">
                @error('role') <div class="text-red-400 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-ice-blue text-deep-navy rounded font-medium">Update Profile</button>

                <button type="button" id="start-tour-btn" class="px-4 py-2 border border-slate-600 rounded text-ice">Start Tour</button>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            var btn = document.getElementById('start-tour-btn');
            if (!btn) return;
            btn.addEventListener('click', function(){
                // Redirect to dashboard and request tour start
                window.location.href = '/home?startTour=1';
            });
        });
    </script>

<!-- </div> -->
@endsection