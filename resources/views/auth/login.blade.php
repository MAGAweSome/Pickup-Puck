@extends('layouts.app')

@section('content')

<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-6 px-3 sm:px-6">
    <div x-data='{"tab": @json(old("form", "login")) }' class="w-full max-w-4xl grid grid-cols-1 lg:grid-cols-12 rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900/70 backdrop-blur-xl">

        <!-- Left Stadium Showcase Brand Banner -->
        <div class="relative hidden lg:flex lg:col-span-5 flex-col justify-between p-8 bg-gradient-to-br from-slate-100 via-sky-50/50 to-slate-200 dark:from-slate-900 dark:via-slate-950 dark:to-sky-950/40 border-r border-slate-200 dark:border-white/10 overflow-hidden">
            <!-- Background Arena Radial Accent -->
            <div class="absolute -top-24 -left-24 w-72 h-72 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-72 h-72 bg-cyan-400/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-4">
                <div class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-full bg-sky-100 dark:bg-sky-500/10 border border-sky-300 dark:border-sky-400/20 text-sky-800 dark:text-sky-300 text-xs font-semibold tracking-wider uppercase">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span>
                    Stadium Arena Access
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-500 to-cyan-300 flex items-center justify-center shadow-lg shadow-sky-500/30 text-slate-950 font-black text-xl tracking-tighter">
                        🏒
                    </div>
                    <div>
                        <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white font-heading">PICKUP PUCK</h2>
                        <p class="text-xs font-semibold text-sky-700 dark:text-sky-400 uppercase tracking-widest">Digital Game Day Hub</p>
                    </div>
                </div>

                <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed pt-2">
                    Pickup hockey scheduling made effortless. Balanced rosters, live goalie tracking, instant roster reveals, and game day notifications.
                </p>
            </div>

            <!-- Arena Feature Bullets -->
            <div class="relative z-10 space-y-3 my-8 pt-4 border-t border-slate-200 dark:border-white/5">
                <div class="flex items-center gap-3 text-xs text-slate-700 dark:text-slate-300">
                    <div class="w-7 h-7 rounded-lg bg-white dark:bg-sky-500/10 border border-slate-200 dark:border-sky-500/20 flex items-center justify-center text-sky-600 dark:text-sky-400 shrink-0 shadow-sm">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <span>Balanced snake coin-flip rosters every game</span>
                </div>
                <div class="flex items-center gap-3 text-xs text-slate-700 dark:text-slate-300">
                    <div class="w-7 h-7 rounded-lg bg-white dark:bg-sky-500/10 border border-slate-200 dark:border-sky-500/20 flex items-center justify-center text-sky-600 dark:text-sky-400 shrink-0 shadow-sm">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <span>Crease locks &amp; live goalie attendance counters</span>
                </div>
                <div class="flex items-center gap-3 text-xs text-slate-700 dark:text-slate-300">
                    <div class="w-7 h-7 rounded-lg bg-white dark:bg-sky-500/10 border border-slate-200 dark:border-sky-500/20 flex items-center justify-center text-sky-600 dark:text-sky-400 shrink-0 shadow-sm">
                        <i class="fa-solid fa-stopwatch"></i>
                    </div>
                    <span>T-30 live jersey reveal &amp; countdown timer</span>
                </div>
            </div>

            <div class="relative z-10 text-[11px] text-slate-500 flex items-center justify-between">
                <span>Designed for fast rinks</span>
                <span class="font-mono text-slate-500 dark:text-slate-400">v2.4 Stadium</span>
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="lg:col-span-7 p-6 sm:p-8 md:p-10 flex flex-col justify-center bg-white dark:bg-slate-900/90">
            <!-- Segmented Pill Switcher -->
            <div class="flex items-center justify-center mb-6">
                <div class="inline-flex p-1 rounded-2xl bg-slate-100 dark:bg-slate-950/80 border border-slate-200 dark:border-white/10 w-full max-w-xs shadow-inner">
                    <button type="button" @click="tab = 'login'"
                        :class="tab === 'login' ? 'bg-gradient-to-r from-sky-500 to-cyan-400 text-slate-950 font-bold shadow-md shadow-sky-500/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
                        class="flex-1 py-2 text-center text-sm rounded-xl transition-all duration-200">
                        <i class="fa-solid fa-right-to-bracket mr-1.5"></i> Sign In
                    </button>
                    <button type="button" @click="tab = 'register'"
                        :class="tab === 'register' ? 'bg-gradient-to-r from-sky-500 to-cyan-400 text-slate-950 font-bold shadow-md shadow-sky-500/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
                        class="flex-1 py-2 text-center text-sm rounded-xl transition-all duration-200">
                        <i class="fa-solid fa-user-plus mr-1.5"></i> Register
                    </button>
                </div>
            </div>

            <!-- Login Form -->
            <form x-show="tab === 'login'" x-cloak method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showLoginPassword: false }">
                @csrf
                <input type="hidden" name="form" value="login">

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        <i class="fa-regular fa-envelope text-sky-600 dark:text-sky-400 mr-1"></i> {{ __('Email Address') }}
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        placeholder="player@arena.ca"
                        class="w-full bg-slate-50 dark:bg-slate-950/70 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 border border-slate-300 dark:border-white/10 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-sky-500 dark:focus:border-sky-400 focus:ring-1 focus:ring-sky-500 dark:focus:ring-sky-400 transition-all shadow-inner" />
                    @if(old('form') === 'login')
                        @error('email') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                    @endif
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-lock text-sky-600 dark:text-sky-400 mr-1"></i> {{ __('Password') }}
                        </label>
                        @if (Route::has('password.request'))
                            <a class="text-xs text-sky-600 hover:text-sky-800 dark:text-sky-400 dark:hover:text-sky-300 transition-colors font-medium" href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
                        @endif
                    </div>
                    <div class="relative">
                        <input id="password" :type="showLoginPassword ? 'text' : 'password'" name="password" required autocomplete="current-password"
                            placeholder="••••••••••••"
                            class="w-full bg-slate-50 dark:bg-slate-950/70 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 border border-slate-300 dark:border-white/10 rounded-xl px-3.5 py-2.5 pr-11 text-sm focus:outline-none focus:border-sky-500 dark:focus:border-sky-400 focus:ring-1 focus:ring-sky-500 dark:focus:ring-sky-400 transition-all shadow-inner" />

                        <button type="button" @click="showLoginPassword = !showLoginPassword" class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors focus:outline-none">
                            <i x-show="!showLoginPassword" x-cloak class="fa-regular fa-eye"></i>
                            <i x-show="showLoginPassword" x-cloak class="fa-regular fa-eye-slash"></i>
                        </button>
                    </div>
                    @if(old('form') === 'login')
                        @error('password') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                    @endif
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300 cursor-pointer select-none">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 text-sky-600 dark:text-sky-500 bg-white dark:bg-slate-950 border-slate-300 dark:border-white/20 rounded focus:ring-sky-500">
                        <span>{{ __('Stay signed in on this device') }}</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 rounded-xl font-bold text-sm text-slate-950 bg-gradient-to-r from-sky-400 via-cyan-300 to-sky-400 hover:from-sky-300 hover:to-cyan-200 shadow-lg shadow-sky-500/25 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                        <span>{{ __('Sign In to Locker Room') }}</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </form>

            <!-- Register Form -->
            @php
                $isRegisterOld = old('form') === 'register' || session('_old_input.form') === 'register';
            @endphp
            <form x-show="tab === 'register'" x-cloak method="POST" action="{{ route('register') }}" class="space-y-4"
                x-data='registerForm({
                    initialName: @json($isRegisterOld ? old("name", "") : ""),
                    initialEmail: @json($isRegisterOld ? old("email", "") : ""),
                    checkEmailUrl: @json(route("register.check_email")),
                    csrfToken: @json(csrf_token())
                })'
                @submit="onSubmit()"
            >
                @csrf
                <input type="hidden" name="form" value="register">

                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        <i class="fa-regular fa-user text-sky-600 dark:text-sky-400 mr-1"></i> {{ __('Player Full Name') }}
                    </label>
                    <input id="name" type="text" name="name" x-model="name" @blur="onNameBlur()" required autocomplete="off" autocapitalize="words" spellcheck="false"
                        placeholder="Connor McDavid"
                        class="w-full bg-slate-50 dark:bg-slate-950/70 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 border border-slate-300 dark:border-white/10 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-sky-500 dark:focus:border-sky-400 focus:ring-1 focus:ring-sky-500 dark:focus:ring-sky-400 transition-all shadow-inner" />
                    @if(old('form') === 'register' || session('_old_input.form') === 'register')
                        @error('name') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                    @endif
                    <div x-show="nameBlurred && (name || '').trim().length > 0 && !nameIsValid()" x-cloak class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> Please enter both first and last name
                    </div>
                </div>

                <div>
                    <label for="reg_email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        <i class="fa-regular fa-envelope text-sky-600 dark:text-sky-400 mr-1"></i> {{ __('Email Address') }}
                    </label>
                    <div class="relative">
                        <input id="reg_email" type="email" name="email" x-model="regEmail" @input="onEmailInput()" @blur="checkEmail()" required autocomplete="email" autocapitalize="none"
                            placeholder="player@arena.ca"
                            class="w-full bg-slate-50 dark:bg-slate-950/70 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 border border-slate-300 dark:border-white/10 rounded-xl px-3.5 py-2.5 pr-9 text-sm focus:outline-none focus:border-sky-500 dark:focus:border-sky-400 focus:ring-1 focus:ring-sky-500 dark:focus:ring-sky-400 transition-all shadow-inner" />
                        <div x-show="emailChecking" x-cloak class="absolute inset-y-0 right-3 flex items-center text-sky-600 dark:text-sky-400">
                            <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                        </div>
                    </div>
                    @if(old('form') === 'register' || session('_old_input.form') === 'register')
                        @error('email') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                    @endif
                    <div x-show="emailChecked && emailExists" x-cloak class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> That email is already registered — <a href="javascript:void(0)" @click="tab = 'login'" class="text-sky-600 dark:text-sky-300 underline font-semibold ml-1">Sign In</a>
                    </div>
                </div>

                <div>
                    <label for="reg_password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        <i class="fa-solid fa-lock text-sky-600 dark:text-sky-400 mr-1"></i> {{ __('Password') }}
                    </label>
                    <div class="relative">
                        <input id="reg_password" :type="showPassword ? 'text' : 'password'" name="password" x-model="password" @blur="passwordTouched = true" @input="passwordTouched = true" required autocomplete="new-password"
                            placeholder="••••••••••••"
                            class="w-full bg-slate-50 dark:bg-slate-950/70 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 border border-slate-300 dark:border-white/10 rounded-xl px-3.5 py-2.5 pr-11 text-sm focus:outline-none focus:border-sky-500 dark:focus:border-sky-400 focus:ring-1 focus:ring-sky-500 dark:focus:ring-sky-400 transition-all shadow-inner" />

                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors focus:outline-none">
                            <i x-show="!showPassword" x-cloak class="fa-regular fa-eye"></i>
                            <i x-show="showPassword" x-cloak class="fa-regular fa-eye-slash"></i>
                        </button>
                    </div>
                    @if(old('form') === 'register' || session('_old_input.form') === 'register')
                        @error('password') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                    @endif

                    <!-- Password Strength Meter -->
                    <div class="mt-2.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-white/5 space-y-2" x-show="(password || '').length > 0" x-cloak x-transition>
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

                <div>
                    <label for="password-confirm" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        <i class="fa-solid fa-lock-open text-sky-600 dark:text-sky-400 mr-1"></i> {{ __('Confirm Password') }}
                    </label>
                    <div class="relative">
                        <input id="password-confirm" :type="showPasswordConfirmation ? 'text' : 'password'" name="password_confirmation" x-model="password_confirmation" @input="passwordTouched = true" required autocomplete="new-password"
                            placeholder="••••••••••••"
                            class="w-full bg-slate-50 dark:bg-slate-950/70 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 border border-slate-300 dark:border-white/10 rounded-xl px-3.5 py-2.5 pr-11 text-sm focus:outline-none focus:border-sky-500 dark:focus:border-sky-400 focus:ring-1 focus:ring-sky-500 dark:focus:ring-sky-400 transition-all shadow-inner" />

                        <button type="button" @click="showPasswordConfirmation = !showPasswordConfirmation" class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors focus:outline-none">
                            <i x-show="!showPasswordConfirmation" x-cloak class="fa-regular fa-eye"></i>
                            <i x-show="showPasswordConfirmation" x-cloak class="fa-regular fa-eye-slash"></i>
                        </button>
                    </div>

                    <!-- Live Match Indicator -->
                    <div class="mt-2 text-xs font-medium" x-show="(password_confirmation || '').length > 0" x-cloak x-transition>
                        <span x-show="passwordsMatch" class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check"></i> Passwords match
                        </span>
                        <span x-show="!passwordsMatch" class="text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation"></i> Passwords do not match yet
                        </span>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 rounded-xl font-bold text-sm text-slate-950 bg-gradient-to-r from-sky-400 via-cyan-300 to-sky-400 hover:from-sky-300 hover:to-cyan-200 shadow-lg shadow-sky-500/25 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                        <span>{{ __('Create Player Account') }}</span>
                        <i class="fa-solid fa-user-check text-xs"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function registerForm({ initialName, initialEmail, checkEmailUrl, csrfToken }) {
        return {
            name: initialName || '',
            regEmail: initialEmail || '',
            password: '',
            password_confirmation: '',
            showPassword: false,
            showPasswordConfirmation: false,
            nameBlurred: false,
            passwordTouched: false,
            emailChecking: false,
            emailExists: false,
            emailChecked: false,

            nameIsValid() {
                const val = (this.name || '').trim();
                return /^[A-Za-z]+ [A-Za-z]{2,}$/.test(val);
            },

            get hasMinLength() {
                return (this.password || '').length >= 8;
            },
            get hasLetter() {
                return /[a-zA-Z]/.test(this.password || '');
            },
            get hasNumberOrSpecial() {
                return /[0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(this.password || '');
            },
            get hasMixedCase() {
                return /[a-z]/.test(this.password || '') && /[A-Z]/.test(this.password || '');
            },
            get strengthScore() {
                if (!this.password || this.password.length === 0) return 0;
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
                return (this.password || '').length > 0 && this.password === this.password_confirmation;
            },

            toTitleCase(s) {
                return String(s || '')
                    .toLowerCase()
                    .split(/\s+/)
                    .filter(Boolean)
                    .map(seg => {
                        return seg
                            .split(/([\'\-])/)
                            .map(part => (part.length > 0 ? part.charAt(0).toUpperCase() + part.slice(1) : part))
                            .join('');
                    })
                    .join(' ');
            },

            onNameBlur() {
                this.name = this.toTitleCase((this.name || '').trim());
                this.nameBlurred = true;
            },

            onEmailInput() {
                this.emailChecked = false;
                this.emailExists = false;
                this.emailChecking = false;
            },

            async checkEmail() {
                const emailVal = String(this.regEmail || '').trim();
                this.emailChecked = true;

                if (!emailVal || !/.+@.+\..+/.test(emailVal)) {
                    this.emailExists = false;
                    return;
                }

                this.emailChecking = true;
                try {
                    const res = await fetch(checkEmailUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({ email: emailVal })
                    });

                    if (res.ok) {
                        const data = await res.json();
                        this.emailExists = !!data.exists;
                    } else {
                        this.emailExists = false;
                    }
                } catch (e) {
                    this.emailExists = false;
                } finally {
                    this.emailChecking = false;
                }
            },

            onSubmit() {
                this.onNameBlur();
                // Never persist passwords in state longer than necessary.
                // Keep models as-is; backend validation will handle the rest.
            }
        };
    }
</script>
@endpush
