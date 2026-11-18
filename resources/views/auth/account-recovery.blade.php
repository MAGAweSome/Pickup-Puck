@extends('layouts.app')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-lg space-y-6">
        <!-- Header Card -->
        <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-cyan-500/30 p-6 sm:p-8 shadow-2xl">
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono font-bold bg-sky-50 dark:bg-cyan-500/15 border border-sky-200 dark:border-cyan-500/30 text-sky-700 dark:text-cyan-300 mb-3">
                    <i class="fa-solid fa-shield-halved text-[11px]"></i>
                    <span>Secure 5-Hour Self-Service Link</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                    Update Your Account &amp; Password
                </h1>
                <p class="mt-1.5 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    Review your name and email address below and choose a new password for your <strong class="text-slate-800 dark:text-slate-200">Pickup Puck</strong> account.
                    @if(isset($expiresAt) && $expiresAt)
                        <span class="block mt-1 font-mono text-[11px] text-sky-600 dark:text-cyan-400">
                            Link expires {{ $expiresAt->diffForHumans() }} ({{ $expiresAt->format('M j, g:i A') }}).
                        </span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 rounded-3xl p-6 sm:p-8 shadow-xl">
            @if ($errors->any())
                <div class="mb-5 p-4 rounded-2xl bg-rose-50 dark:bg-rose-500/15 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-xs sm:text-sm space-y-1">
                    <div class="font-bold flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Please fix the following errors:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 pl-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $actionUrl }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="name" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Full Name
                    </label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        autocomplete="name"
                        class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition shadow-inner"
                    >
                </div>

                <div>
                    <label for="email" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Email Address
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        autocomplete="email"
                        class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition shadow-inner"
                    >
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-4">
                    <div>
                        <label for="password" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            New Password
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Minimum 8 characters"
                            class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition shadow-inner"
                        >
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Confirm New Password
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Re-enter your new password"
                            class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition shadow-inner"
                        >
                    </div>
                </div>

                <div class="pt-3">
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 dark:from-cyan-500 dark:to-sky-500 dark:hover:from-cyan-400 dark:hover:to-sky-400 text-white dark:text-slate-950 font-black rounded-xl text-sm shadow-lg transition cursor-pointer"
                    >
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
