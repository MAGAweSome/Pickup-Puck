@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-slate-900 border border-slate-700 rounded-lg shadow-md px-6 py-8">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-ice-blue mb-2">{{ __('Reset Password') }}</h2>
            <p class="text-sm text-slate-300">Enter your email and we'll send you a password reset link.</p>
        </div>

        @if (session('status'))
            <div class="mb-4 p-3 rounded bg-emerald-600/20 border border-emerald-500/30 text-emerald-200 text-sm" role="alert">
                {{ session('status') }}
            </div>
            <div class="mb-4 p-3 rounded bg-sky-600/15 border border-sky-500/25 text-sky-200 text-sm flex items-start gap-2" role="alert">
                <i class="fa-solid fa-circle-info mt-0.5 text-sky-400"></i>
                <span>Can't find the email? Please check your <strong>Spam</strong> or <strong>Junk</strong> folder — it may have been filtered there.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm text-gray-100 mb-1">{{ __('Email Address') }}</label>
                <input id="email" type="email" class="w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue @error('email') border-red-500 @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                    <div class="text-red-400 text-sm mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-ice-blue text-deep-navy font-semibold py-2.5 rounded shadow hover:bg-ice-blue/90 transition">
                    {{ __('Send Password Reset Link') }}
                </button>
            </div>

            <div class="text-center pt-2">
                <a href="{{ route('login') }}" class="text-sm text-slate-400 hover:text-white underline">
                    Back to Login
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
