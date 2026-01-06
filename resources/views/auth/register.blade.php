@extends('layouts.app')

@section('content')

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-slate-900 border border-slate-700 rounded-lg shadow-md px-6 py-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-white">{{ __('Register') }}</h2>
            <a href="{{ route('login') }}" class="text-slate-300 hover:text-white">{{ __('Login') }}</a>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm text-gray-100">{{ __('Name') }}</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                    class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
                @error('name') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm text-gray-100">{{ __('Email Address') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                    class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
                @error('email') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm text-gray-100">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
                @error('password') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="password-confirm" class="block text-sm text-gray-100">{{ __('Confirm Password') }}</label>
                <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
            </div>

            <div>
                <button type="submit" class="w-full bg-ice-blue text-deep-navy font-semibold py-2 rounded shadow">{{ __('Register') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection
