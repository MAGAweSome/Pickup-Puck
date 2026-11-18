@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md bg-white dark:bg-[#1e293b] border border-rose-200 dark:border-rose-500/30 rounded-3xl p-7 sm:p-9 shadow-2xl text-center space-y-5">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-50 dark:bg-rose-500/15 border border-rose-200 dark:border-rose-500/30 flex items-center justify-center text-rose-600 dark:text-rose-400 text-2xl">
            <i class="fa-solid fa-link-slash"></i>
        </div>

        <div class="space-y-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-mono font-bold uppercase tracking-wider bg-rose-50 dark:bg-rose-500/15 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30">
                Link Expired or Invalid
            </span>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                This Account Update Link Has Expired
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                For your security, Pickup Puck recovery links are single-use and expire after <strong class="text-slate-800 dark:text-slate-200">5 hours</strong>. This link has either expired, been altered, or has already been used.
            </p>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-300">
            Please ask a league administrator to generate a new 5-hour recovery link for your account.
        </div>

        <div class="pt-1">
            <a
                href="{{ route('login') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-cyan-500 dark:hover:bg-cyan-400 text-white dark:text-slate-950 font-bold rounded-xl text-xs sm:text-sm transition no-underline shadow-md"
            >
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Return to Sign In</span>
            </a>
        </div>
    </div>
</div>
@endsection
