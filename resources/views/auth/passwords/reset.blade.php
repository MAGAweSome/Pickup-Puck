@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-slate-900 border border-slate-700 rounded-lg shadow-xl px-6 py-8" x-data="resetPasswordValidator()">
        <div class="mb-6 pb-3 border-b border-slate-800">
            <h2 class="text-2xl font-bold text-ice-blue">{{ __('Reset Password') }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">Set a secure new password for your account</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-200 mb-1">{{ __('Email Address') }}</label>
                <input id="email" type="email" class="w-full bg-slate-800 text-ice border border-slate-700 rounded-md px-3.5 py-2 text-sm focus:outline-none focus:border-ice-blue focus:ring-1 focus:ring-ice-blue transition @error('email') border-rose-500 @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                    <div class="text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- New Password with Live Strength UI -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-200 mb-1">{{ __('New Password') }}</label>
                <div class="relative">
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="w-full bg-slate-800 text-ice border border-slate-700 rounded-md pl-3.5 pr-10 py-2 text-sm focus:outline-none focus:border-ice-blue focus:ring-1 focus:ring-ice-blue transition @error('password') border-rose-500 @enderror"
                        name="password"
                        x-model="password"
                        required
                        autocomplete="new-password"
                        placeholder="At least 8 characters"
                    >
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-ice focus:outline-none"
                        tabindex="-1"
                        aria-label="Toggle password visibility"
                    >
                        <i :class="showPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                    </button>
                </div>

                @error('password')
                    <div class="text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                <!-- Password Strength Meter -->
                <div class="mt-2 space-y-1.5" x-show="password.length > 0" x-transition>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-400">Password Strength:</span>
                        <span class="font-semibold" :class="strengthTextColor" x-text="strengthLabel"></span>
                    </div>
                    <div class="grid grid-cols-4 gap-1.5 h-1.5 w-full bg-slate-950 rounded-full overflow-hidden p-0.5">
                        <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 1 ? strengthBgColor : 'bg-transparent'"></div>
                        <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 2 ? strengthBgColor : 'bg-transparent'"></div>
                        <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 3 ? strengthBgColor : 'bg-transparent'"></div>
                        <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 4 ? strengthBgColor : 'bg-transparent'"></div>
                    </div>

                    <!-- Requirements Checklist -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1.5 pt-1 text-xs">
                        <div class="flex items-center gap-1.5" :class="hasMinLength ? 'text-emerald-400' : 'text-slate-500'">
                            <i :class="hasMinLength ? 'fa-solid fa-check-circle' : 'fa-regular fa-circle'"></i>
                            <span>8+ characters</span>
                        </div>
                        <div class="flex items-center gap-1.5" :class="hasLetter ? 'text-emerald-400' : 'text-slate-500'">
                            <i :class="hasLetter ? 'fa-solid fa-check-circle' : 'fa-regular fa-circle'"></i>
                            <span>Letters</span>
                        </div>
                        <div class="flex items-center gap-1.5" :class="hasNumberOrSpecial ? 'text-emerald-400' : 'text-slate-500'">
                            <i :class="hasNumberOrSpecial ? 'fa-solid fa-check-circle' : 'fa-regular fa-circle'"></i>
                            <span>Numbers/symbols</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirm New Password -->
            <div>
                <label for="password-confirm" class="block text-sm font-medium text-slate-200 mb-1">{{ __('Confirm New Password') }}</label>
                <div class="relative">
                    <input
                        id="password-confirm"
                        :type="showConfirm ? 'text' : 'password'"
                        class="w-full bg-slate-800 text-ice border border-slate-700 rounded-md pl-3.5 pr-10 py-2 text-sm focus:outline-none focus:border-ice-blue focus:ring-1 focus:ring-ice-blue transition"
                        name="password_confirmation"
                        x-model="confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Repeat your new password"
                    >
                    <button
                        type="button"
                        @click="showConfirm = !showConfirm"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-ice focus:outline-none"
                        tabindex="-1"
                        aria-label="Toggle confirm password visibility"
                    >
                        <i :class="showConfirm ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                    </button>
                </div>

                <!-- Live Match Indicator -->
                <div class="mt-1.5 text-xs" x-show="confirmation.length > 0" x-transition>
                    <span x-show="passwordsMatch" class="text-emerald-400 flex items-center gap-1">
                        <i class="fa-solid fa-check"></i> Passwords match
                    </span>
                    <span x-show="!passwordsMatch" class="text-amber-400 flex items-center gap-1">
                        <i class="fa-solid fa-triangle-exclamation"></i> Passwords do not match yet
                    </span>
                </div>
            </div>

            <div class="pt-3">
                <button type="submit" class="w-full bg-ice-blue text-deep-navy font-bold py-2.5 rounded-md shadow-md hover:bg-ice transition flex items-center justify-center gap-2 text-sm">
                    <i class="fa-solid fa-key text-xs"></i>
                    <span>{{ __('Reset Password') }}</span>
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
