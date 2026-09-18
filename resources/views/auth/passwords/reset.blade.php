@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-3xl shadow-2xl p-6 sm:p-8 relative overflow-hidden" x-data="resetPasswordValidator()">
        <!-- Background Radial Glow -->
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-sky-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 mb-6 pb-4 border-b border-slate-200 dark:border-white/10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-100 dark:bg-sky-500/10 border border-sky-300 dark:border-sky-400/20 text-sky-800 dark:text-sky-400 text-xs font-semibold uppercase tracking-wider mb-3">
                <i class="fa-solid fa-lock-open text-[10px]"></i> Security Update
            </div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white font-heading tracking-tight">{{ __('Reset Password') }}</h2>
            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">Set a secure new password for your player account</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="relative z-10 space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                    <i class="fa-regular fa-envelope text-sky-600 dark:text-sky-400 mr-1"></i> {{ __('Email Address') }}
                </label>
                <input id="email" type="email" class="w-full bg-slate-50 dark:bg-slate-950/70 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 border border-slate-300 dark:border-white/10 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-sky-500 dark:focus:border-sky-400 focus:ring-1 focus:ring-sky-500 dark:focus:ring-sky-400 transition shadow-inner @error('email') border-rose-500 @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                    <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- New Password with Live Strength UI -->
            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                    <i class="fa-solid fa-lock text-sky-600 dark:text-sky-400 mr-1"></i> {{ __('New Password') }}
                </label>
                <div class="relative">
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="w-full bg-slate-50 dark:bg-slate-950/70 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 border border-slate-300 dark:border-white/10 rounded-xl px-3.5 py-2.5 pr-11 text-sm focus:outline-none focus:border-sky-500 dark:focus:border-sky-400 focus:ring-1 focus:ring-sky-500 dark:focus:ring-sky-400 transition shadow-inner @error('password') border-rose-500 @enderror"
                        name="password"
                        x-model="password"
                        required
                        autocomplete="new-password"
                        placeholder="At least 8 characters"
                    >
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors focus:outline-none"
                        tabindex="-1"
                        aria-label="Toggle password visibility"
                    >
                        <i :class="showPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                    </button>
                </div>

                @error('password')
                    <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                <!-- Password Strength Meter -->
                <div class="mt-2.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-white/5 space-y-2" x-show="password.length > 0" x-transition>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-600 dark:text-slate-400">Password Strength:</span>
                        <span class="font-bold tracking-wide" :class="strengthTextColor" x-text="strengthLabel"></span>
                    </div>
                    <div class="grid grid-cols-4 gap-1.5 h-1.5 w-full bg-slate-200 dark:bg-slate-900 rounded-full overflow-hidden p-0.5">
                        <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 1 ? strengthBgColor : 'bg-transparent'"></div>
                        <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 2 ? strengthBgColor : 'bg-transparent'"></div>
                        <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 3 ? strengthBgColor : 'bg-transparent'"></div>
                        <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 4 ? strengthBgColor : 'bg-transparent'"></div>
                    </div>

                    <!-- Requirements Checklist -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1.5 pt-1 text-[11px]">
                        <div class="flex items-center gap-1.5" :class="hasMinLength ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500'">
                            <i :class="hasMinLength ? 'fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400' : 'fa-regular fa-circle'"></i>
                            <span>8+ characters</span>
                        </div>
                        <div class="flex items-center gap-1.5" :class="hasLetter ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500'">
                            <i :class="hasLetter ? 'fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400' : 'fa-regular fa-circle'"></i>
                            <span>Letters</span>
                        </div>
                        <div class="flex items-center gap-1.5" :class="hasNumberOrSpecial ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500'">
                            <i :class="hasNumberOrSpecial ? 'fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400' : 'fa-regular fa-circle'"></i>
                            <span>Numbers / symbols</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirm New Password -->
            <div>
                <label for="password-confirm" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                    <i class="fa-solid fa-lock-open text-sky-600 dark:text-sky-400 mr-1"></i> {{ __('Confirm New Password') }}
                </label>
                <div class="relative">
                    <input
                        id="password-confirm"
                        :type="showConfirm ? 'text' : 'password'"
                        class="w-full bg-slate-50 dark:bg-slate-950/70 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 border border-slate-300 dark:border-white/10 rounded-xl px-3.5 py-2.5 pr-11 text-sm focus:outline-none focus:border-sky-500 dark:focus:border-sky-400 focus:ring-1 focus:ring-sky-500 dark:focus:ring-sky-400 transition shadow-inner"
                        name="password_confirmation"
                        x-model="confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Repeat your new password"
                    >
                    <button
                        type="button"
                        @click="showConfirm = !showConfirm"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors focus:outline-none"
                        tabindex="-1"
                        aria-label="Toggle confirm password visibility"
                    >
                        <i :class="showConfirm ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                    </button>
                </div>

                <!-- Live Match Indicator -->
                <div class="mt-2 text-xs font-medium" x-show="confirmation.length > 0" x-transition>
                    <span x-show="passwordsMatch" class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check"></i> Passwords match
                    </span>
                    <span x-show="!passwordsMatch" class="text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-triangle-exclamation"></i> Passwords do not match yet
                    </span>
                </div>
            </div>

            <div class="pt-3">
                <button type="submit" class="w-full py-3 rounded-xl font-bold text-sm text-slate-950 bg-gradient-to-r from-sky-400 via-cyan-300 to-sky-400 hover:from-sky-300 hover:to-cyan-200 shadow-lg shadow-sky-500/25 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-key text-xs"></i>
                    <span>{{ __('Update Password') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function resetPasswordValidator() {
        return {
            showPassword: false,
            showConfirm: false,
            password: '',
            confirmation: '',

            get hasMinLength() {
                return this.password.length >= 8;
            },
            get hasLetter() {
                return /[a-zA-Z]/.test(this.password);
            },
            get hasNumberOrSpecial() {
                return /[0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(this.password);
            },
            get hasMixedCase() {
                return /[a-z]/.test(this.password) && /[A-Z]/.test(this.password);
            },
            get strengthScore() {
                if (this.password.length === 0) return 0;
                let score = 0;
                if (this.hasMinLength) score++;
                if (this.hasLetter) score++;
                if (this.hasNumberOrSpecial) score++;
                if (this.hasMixedCase || this.password.length >= 12) score++;
                return Math.min(4, Math.max(1, score));
            },
            get strengthLabel() {
                switch(this.strengthScore) {
                    case 1: return 'Weak';
                    case 2: return 'Fair';
                    case 3: return 'Good';
                    case 4: return 'Strong';
                    default: return '';
                }
            },
            get strengthTextColor() {
                switch(this.strengthScore) {
                    case 1: return 'text-rose-400';
                    case 2: return 'text-amber-400';
                    case 3: return 'text-ice-blue';
                    case 4: return 'text-emerald-400';
                    default: return 'text-slate-400';
                }
            },
            get strengthBgColor() {
                switch(this.strengthScore) {
                    case 1: return 'bg-rose-500';
                    case 2: return 'bg-amber-500';
                    case 3: return 'bg-ice-blue';
                    case 4: return 'bg-emerald-500';
                    default: return 'bg-slate-700';
                }
            },
            get passwordsMatch() {
                return this.password.length > 0 && this.password === this.confirmation;
            }
        };
    }
</script>
@endsection
