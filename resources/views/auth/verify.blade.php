@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-3xl shadow-2xl p-6 sm:p-8 text-center relative overflow-hidden" id="verifyCard">
        <!-- Background Radial Glow -->
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-sky-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-sky-500 to-cyan-400 text-slate-950 flex items-center justify-center mx-auto mb-4 text-2xl shadow-lg shadow-sky-500/25">
            <i class="fa-regular fa-envelope"></i>
        </div>

        <h2 class="text-2xl font-black text-slate-900 dark:text-white font-heading tracking-tight mb-2">{{ __('Verify Your Email') }}</h2>

        <p class="text-xs text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
            {{ __('Before proceeding, please check your email inbox for the verification link we just sent.') }}
        </p>

        <!-- Live Listening Indicator -->
        <div id="listeningIndicator" class="mb-4 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-100 dark:bg-slate-950/80 border border-slate-200 dark:border-white/10 text-xs text-slate-700 dark:text-slate-300 shadow-inner">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-sky-500"></span>
            </span>
            <span>Waiting for confirmation (auto-refreshes when clicked)...</span>
        </div>

        <!-- Verified Success Banner (Hidden until verified) -->
        <div id="verifiedSuccessAlert" class="hidden mb-4 p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-300 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-xs flex items-center justify-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-base"></i>
            <span><strong>Email Verified!</strong> Loading your locker room dashboard...</span>
        </div>

        <div class="mb-5 p-3.5 rounded-xl bg-sky-50 dark:bg-sky-500/10 border border-sky-200 dark:border-sky-500/20 text-sky-900 dark:text-sky-200 text-xs flex items-start gap-2.5 text-left" role="alert">
            <i class="fa-solid fa-circle-info mt-0.5 text-sky-600 dark:text-sky-400 text-sm shrink-0"></i>
            <span>Can't find the email? Please check your <strong>Spam</strong> or <strong>Junk</strong> folder — it may have been filtered there.</span>
        </div>

        @if (session('resent'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-300 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-200 text-xs text-left flex items-center gap-2" role="alert">
                <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400"></i>
                <span>{{ __('A fresh verification link has been sent to your email address.') }}</span>
            </div>
        @endif

        <div class="border-t border-slate-200 dark:border-white/10 pt-4 mt-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">{{ __('If you did not receive the email') }}:</p>
            <form method="POST" action="{{ route('verification.resend') }}" id="resendForm">
                @csrf
                <button type="submit" id="resendBtn" class="px-5 py-2.5 rounded-xl font-bold text-xs text-slate-950 bg-gradient-to-r from-sky-400 via-cyan-300 to-sky-400 hover:from-sky-300 hover:to-cyan-200 shadow-md shadow-sky-500/20 active:scale-[0.99] transition-all flex items-center justify-center gap-1.5 mx-auto">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>{{ __('Request Another Link') }}</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        let isRedirecting = false;
        const statusUrl = '{{ route('verification.status') }}';
        const homeUrl = '{{ route('home') }}';

        // Poll every 3 seconds to detect when user confirms email from their inbox
        const checkInterval = setInterval(async () => {
            if (isRedirecting) return;

            try {
                const res = await fetch(statusUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (res.ok) {
                    const data = await res.json();
                    if (data.verified) {
                        isRedirecting = true;
                        clearInterval(checkInterval);

                        const listeningEl = document.getElementById('listeningIndicator');
                        const successEl = document.getElementById('verifiedSuccessAlert');
                        if (listeningEl) listeningEl.classList.add('hidden');
                        if (successEl) successEl.classList.remove('hidden');

                        if (window.showToast) {
                            window.showToast('Email verified successfully! Redirecting...', 'success');
                        }

                        setTimeout(() => {
                            window.location.href = homeUrl;
                        }, 800);
                    }
                }
            } catch (err) {
                // Ignore transient network errors during background polling
            }
        }, 3000);

        // Async handling for the resend button
        const resendForm = document.getElementById('resendForm');
        const resendBtn = document.getElementById('resendBtn');
        if (resendForm && resendBtn) {
            resendForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const originalHtml = resendBtn.innerHTML;
                resendBtn.disabled = true;
                resendBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> <span>Sending...</span>';

                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const res = await fetch(resendForm.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrf,
                        }
                    });

                    if (res.ok || res.status === 202) {
                        if (window.showToast) {
                            window.showToast('Verification email resent! Check your inbox or junk folder.', 'success');
                        } else {
                            alert('Verification email resent! Check your inbox or junk folder.');
                        }
                    } else if (res.status === 429) {
                        if (window.showToast) {
                            window.showToast('Please wait a moment before requesting another link.', 'error');
                        }
                    } else {
                        resendForm.submit();
                        return;
                    }
                } catch(e) {
                    resendForm.submit();
                    return;
                } finally {
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = originalHtml;
                }
            });
        }
    })();
</script>
@endsection
