@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold">Settings</h1>
        <p class="mt-2 text-slate-300">Application defaults and preferences.</p>

        @if(session('success'))
            <div class="mt-4 p-3 rounded bg-emerald-600 text-white">{{ session('success') }}</div>
        @endif

        <form action="{{ route('settings.update') }}" method="POST" class="mt-6 bg-slate-800 border border-slate-700 rounded-lg p-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 text-sm">Default Role Preference</label>
                    <input type="text" name="default_role_preference" value="{{ old('default_role_preference', $defaults['default_role_preference'] ?? '') }}" class="mt-1 w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">
                </div>

                <div>
                    <label class="block text-slate-300 text-sm">Default Game Role</label>
                    <input type="text" name="default_game_role" value="{{ old('default_game_role', $defaults['default_game_role'] ?? '') }}" class="mt-1 w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="px-4 py-2 bg-ice-blue text-deep-navy rounded">Save</button>
            </div>
        </form>
    </div>
@endsection
