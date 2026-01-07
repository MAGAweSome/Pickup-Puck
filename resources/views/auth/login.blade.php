@extends('layouts.app')

@section('content')

<div class="parent-container d-flex align-items-center justify-content-center h-100">
    <div x-data="{ tab: 'login' }" class="w-full max-w-3xl grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="hidden md:flex flex-col justify-center bg-slate-800 p-8 rounded-lg">
            <h3 class="text-2xl font-semibold text-ice-blue mb-2">Pickup Puck</h3>
            <p class="text-slate-300">Pickup hockey scheduling made simple — manage games, players and payments.</p>
        </div>

        <div class="bg-slate-900 border border-slate-700 rounded-lg shadow-md px-6 py-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <button @click="tab = 'login'" :class="tab === 'login' ? 'bg-ice-blue text-deep-navy' : 'text-slate-300'" class="px-3 py-1 rounded font-medium">Login</button>
                    <button @click="tab = 'register'" :class="tab === 'register' ? 'bg-ice-blue text-deep-navy' : 'text-slate-300'" class="px-3 py-1 rounded font-medium">Register</button>
                </div>
            </div>

            <!-- Login Form -->
            <form x-show="tab === 'login'" x-cloak method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm text-gray-100">{{ __('Email Address') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
                    @error('email') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm text-gray-100">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
                    @error('password') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-slate-300">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} class="h-4 w-4 text-ice-blue bg-slate-800 border-slate-600 rounded">
                        {{ __('Remember Me') }}
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-sm text-slate-300 hover:text-white" href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
                    @endif
                </div>

                <div>
                    <button type="submit" class="w-full bg-ice-blue text-deep-navy font-semibold py-2 rounded shadow">{{ __('Login') }}</button>
                </div>
            </form>

            <!-- Register Form -->
            <form x-show="tab === 'register'" x-cloak method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm text-gray-100">{{ __('Name') }}</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                        class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
                    @error('name') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="reg_email" class="block text-sm text-gray-100">{{ __('Email Address') }}</label>
                    <input id="reg_email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                        class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
                    @error('email') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="reg_password" class="block text-sm text-gray-100">{{ __('Password') }}</label>
                    <input id="reg_password" type="password" name="password" required autocomplete="new-password"
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
</div>

@endsection
