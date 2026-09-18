@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-3xl shadow-2xl p-6 sm:p-8 relative overflow-hidden">
        <!-- Background Radial Glow -->
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-sky-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 mb-6 pb-4 border-b border-slate-200 dark:border-white/10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-100 dark:bg-sky-500/10 border border-sky-300 dark:border-sky-400/20 text-sky-800 dark:text-sky-400 text-xs font-semibold uppercase tracking-wider mb-3">
                <i class="fa-solid fa-key text-[10px]"></i> Account Recovery
            </div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white font-heading tracking-tight">{{ __('Reset Password') }}</h2>
            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">Enter your registered email address and we'll send a secure password reset link.</p>
        </div>

        @if (session('status'))
            <div class="mb-4 p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-300 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2" role="alert">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                <span>{{ session('status') }}</span>
            </div>
            <div class="mb-4 p-3.5 rounded-xl bg-sky-50 dark:bg-sky-500/10 border border-sky-200 dark:border-sky-500/20 text-sky-900 dark:text-sky-200 text-xs flex items-start gap-2.5" role="alert">
                <i class="fa-solid fa-circle-info mt-0.5 text-sky-600 dark:text-sky-400"></i>
                <span>Can't find the email? Please check your <strong>Spam</strong> or <strong>Junk</strong> folder — it may have been filtered there.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="relative z-10 space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                    <i class="fa-regular fa-envelope text-sky-600 dark:text-sky-400 mr-1"></i> {{ __('Email Address') }}
                </label>
                <input id="email" type="email"
                    class="w-full bg-slate-50 dark:bg-slate-950/70 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 border border-slate-300 dark:border-white/10 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-sky-500 dark:focus:border-sky-400 focus:ring-1 focus:ring-sky-500 dark:focus:ring-sky-400 transition shadow-inner @error('email') border-rose-500 @enderror"
                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                    placeholder="player@arena.ca">

                @error('email')
                    <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 rounded-xl font-bold text-sm text-slate-950 bg-gradient-to-r from-sky-400 via-cyan-300 to-sky-400 hover:from-sky-300 hover:to-cyan-200 shadow-lg shadow-sky-500/25 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                    <span>{{ __('Send Password Reset Link') }}</span>
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </div>

            <div class="text-center pt-2">
                <a href="{{ route('login') }}" class="text-xs text-slate-600 dark:text-slate-400 hover:text-sky-600 dark:hover:text-sky-300 transition-colors inline-flex items-center gap-1.5 font-medium">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>Back to Sign In</span>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
