@extends('layouts.app')

@section('content')

<div class="min-h-[85vh] flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl p-6 sm:p-8 transition-colors duration-200" x-data="registerPasswordValidator()">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">{{ __('Create Account') }}</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Join the Pickup Puck hockey league</p>
            </div>
            <a href="{{ route('login') }}" class="text-xs text-sky-600 dark:text-cyan-400 hover:underline font-semibold">{{ __('Login') }}</a>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Full Name') }}</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                    placeholder="e.g. Wayne Gretzky"
                    class="w-full bg-slate-50 dark:bg-slate-800/80 text-slate-900 dark:text-white border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-sky-500 dark:focus:border-cyan-400 focus:ring-2 focus:ring-sky-500/20 dark:focus:ring-cyan-400/20 transition @error('name') border-rose-500 @enderror" />
                @error('name')
                    <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Email Address') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                    placeholder="name@example.com"
                    class="w-full bg-slate-50 dark:bg-slate-800/80 text-slate-900 dark:text-white border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-sky-500 dark:focus:border-cyan-400 focus:ring-2 focus:ring-sky-500/20 dark:focus:ring-cyan-400/20 transition @error('email') border-rose-500 @enderror" />
                @error('email')
                    <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-xs"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- Password with Live Strength UI -->
            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Password') }}</label>
                <div class="relative">
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        x-model="password"
                        required
                        autocomplete="new-password"
                        placeholder="At least 8 characters"
                        class="w-full bg-slate-50 dark:bg-slate-800/80 text-slate-900 dark:text-white border border-slate-300 dark:border-slate-700 rounded-xl pl-4 pr-11 py-2.5 text-sm focus:outline-none focus:border-sky-500 dark:focus:border-cyan-400 focus:ring-2 focus:ring-sky-500/20 dark:focus:ring-cyan-400/20 transition @error('password') border-rose-500 @enderror"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none"
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
                <div class="mt-2.5 space-y-1.5" x-show="password.length > 0" x-transition>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Password Strength:</span>
                        <span class="font-bold" :class="strengthTextColor" x-text="strengthLabel"></span>
                    </div>
                    <div class="grid grid-cols-4 gap-1.5 h-2 w-full bg-slate-200 dark:bg-slate-950 rounded-full overflow-hidden p-0.5">
                        <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 1 ? strengthBgColor : 'bg-transparent'"></div>
                        <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 2 ? strengthBgColor : 'bg-transparent'"></div>
                        <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 3 ? strengthBgColor : 'bg-transparent'"></div>
                        <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 4 ? strengthBgColor : 'bg-transparent'"></div>
                    </div>

                    <!-- Requirements Checklist -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1.5 pt-1.5 text-xs">
                        <div class="flex items-center gap-1.5" :class="hasMinLength ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-slate-400 dark:text-slate-500'">
                            <i :class="hasMinLength ? 'fa-solid fa-check-circle' : 'fa-regular fa-circle'"></i>
                            <span>8+ characters</span>
                        </div>
                        <div class="flex items-center gap-1.5" :class="hasLetter ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-slate-400 dark:text-slate-500'">
                            <i :class="hasLetter ? 'fa-solid fa-check-circle' : 'fa-regular fa-circle'"></i>
                            <span>Letters</span>
                        </div>
                        <div class="flex items-center gap-1.5" :class="hasNumberOrSpecial ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-slate-400 dark:text-slate-500'">
                            <i :class="hasNumberOrSpecial ? 'fa-solid fa-check-circle' : 'fa-regular fa-circle'"></i>
                            <span>Numbers/symbols</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password-confirm" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Confirm Password') }}</label>
                <div class="relative">
                    <input
                        id="password-confirm"
                        :type="showConfirm ? 'text' : 'password'"
                        name="password_confirmation"
                        x-model="confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Repeat your password"
                        class="w-full bg-slate-50 dark:bg-slate-800/80 text-slate-900 dark:text-white border border-slate-300 dark:border-slate-700 rounded-xl pl-4 pr-11 py-2.5 text-sm focus:outline-none focus:border-sky-500 dark:focus:border-cyan-400 focus:ring-2 focus:ring-sky-500/20 dark:focus:ring-cyan-400/20 transition"
                    />
                    <button
                        type="button"
                        @click="showConfirm = !showConfirm"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none"
                        tabindex="-1"
                        aria-label="Toggle confirm password visibility"
                    >
                        <i :class="showConfirm ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                    </button>
                </div>

                <!-- Live Match Indicator -->
                <div class="mt-2 text-xs" x-show="confirmation.length > 0" x-transition>
                    <span x-show="passwordsMatch" class="text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i> Passwords match
                    </span>
                    <span x-show="!passwordsMatch" class="text-amber-600 dark:text-amber-400 font-medium flex items-center gap-1.5">
                        <i class="fa-solid fa-triangle-exclamation"></i> Passwords do not match yet
                    </span>
                </div>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full py-3 px-4 rounded-xl bg-sky-600 hover:bg-sky-500 text-white dark:bg-cyan-400 dark:hover:bg-cyan-300 dark:text-slate-950 font-bold shadow-lg shadow-sky-600/20 dark:shadow-cyan-400/20 transition flex items-center justify-center gap-2 text-sm"
                >
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    <span>{{ __('Create Account') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function registerPasswordValidator() {
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
