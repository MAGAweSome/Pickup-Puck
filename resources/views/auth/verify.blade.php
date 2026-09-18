@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-slate-900 border border-slate-700 rounded-lg shadow-xl px-6 py-8 text-center" id="verifyCard">
        <div class="w-14 h-14 rounded-2xl bg-ice-blue/10 border border-ice-blue/20 text-ice-blue flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="fa-regular fa-envelope"></i>
        </div>

        <h2 class="text-2xl font-bold text-ice-blue mb-2">{{ __('Verify Your Email Address') }}</h2>

        <p class="text-sm text-slate-300 mb-3">
            {{ __('Before proceeding, please check your email for a verification link.') }}
        </p>

        <!-- Live Listening Indicator -->
        <div id="listeningIndicator" class="mb-4 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-800/90 border border-slate-700 text-xs text-slate-300">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-sky-500"></span>
            </span>
            <span>Waiting for confirmation (auto-refreshes when verified)...</span>
        </div>

        <!-- Verified Success Banner (Hidden until verified) -->
        <div id="verifiedSuccessAlert" class="hidden mb-4 p-3.5 rounded-md bg-emerald-600/20 border border-emerald-500/40 text-emerald-200 text-sm flex items-center justify-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
            <span><strong>Email Verified!</strong> Loading your dashboard...</span>
        </div>

        <div class="mb-5 p-3 rounded-md bg-sky-950/40 border border-sky-500/25 text-sky-200 text-xs flex items-start gap-2.5 text-left" role="alert">
            <i class="fa-solid fa-circle-info mt-0.5 text-sky-400 text-sm shrink-0"></i>
            <span>Can't find the email? Please check your <strong>Spam</strong> or <strong>Junk</strong> folder — it may have been filtered there.</span>
        </div>

        @if (session('resent'))
            <div class="mb-4 p-3 rounded-md bg-emerald-600/20 border border-emerald-500/30 text-emerald-200 text-xs text-left flex items-center gap-2" role="alert">
                <i class="fa-solid fa-check text-emerald-400"></i>
                <span>{{ __('A fresh verification link has been sent to your email address.') }}</span>
            </div>
        @endif

        <div class="border-t border-slate-800 pt-4 mt-2">
            <p class="text-xs text-slate-400 mb-3">{{ __('If you did not receive the email') }}:</p>
            <form method="POST" action="{{ route('verification.resend') }}" id="resendForm">
                @csrf
                <button type="submit" id="resendBtn" class="px-5 py-2 bg-ice-blue text-deep-navy font-bold rounded-md shadow hover:bg-ice transition text-xs flex items-center justify-center gap-1.5 mx-auto">
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
