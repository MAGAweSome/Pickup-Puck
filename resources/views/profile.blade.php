@extends('layouts.app')

@section('content')

    <div class="max-w-4xl 2xl:max-w-5xl mx-auto space-y-8">
        <!-- Player Locker Room Header -->
        <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-gradient-to-r dark:from-slate-950/90 dark:via-slate-900/90 dark:to-cyan-950/30 border border-slate-200 dark:border-cyan-500/20 p-6 sm:p-7 shadow-xl backdrop-blur-md">
            @if(Auth::user()->hasVerifiedEmail())
                <!-- Ambient Green Spotlight Glow for Verified Account -->
                <div class="absolute -top-16 -right-16 w-72 h-72 bg-emerald-500/15 dark:bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-16 right-1/4 w-48 h-48 bg-emerald-500/10 dark:bg-emerald-400/10 rounded-full blur-2xl pointer-events-none"></div>
            @endif

            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-sky-50 dark:bg-gradient-to-br dark:from-cyan-500/20 dark:to-sky-600/30 border border-sky-200 dark:border-cyan-400/40 flex items-center justify-center text-sky-700 dark:text-cyan-300 text-2xl font-black font-mono shadow-sm dark:shadow-[0_0_20px_rgba(56,189,248,0.2)] shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-widest text-sky-700 dark:text-cyan-400 bg-sky-50 dark:bg-cyan-500/10 border border-sky-200 dark:border-cyan-500/20 mb-1">
                            Player Locker Room
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ Auth::user()->name }}</h1>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">{{ Auth::user()->email }} • Preferred: <strong class="text-sky-600 dark:text-cyan-300 font-mono">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', Auth::user()->role_preference ?? 'Player')) }}</strong></p>
                    </div>
                </div>

                @if(Auth::user()->hasVerifiedEmail())
                    <div class="sm:self-center self-start flex items-center gap-3 px-4 py-2.5 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/15 border border-emerald-500/30 dark:border-emerald-400/30 text-emerald-800 dark:text-emerald-300 shadow-[0_0_25px_rgba(16,185,129,0.2)] dark:shadow-[0_0_30px_rgba(16,185,129,0.25)] backdrop-blur-sm">
                        <span class="relative flex h-2.5 w-2.5 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        <div class="flex items-center gap-2 font-mono text-xs font-bold tracking-wide">
                            <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-base"></i>
                            <span class="uppercase tracking-wider">Account Verified</span>
                        </div>
                    </div>
                @else
                    <div class="sm:self-center self-start flex items-center gap-2.5 px-4 py-2 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-800 dark:text-amber-300 text-xs font-mono font-bold">
                        <i class="fa-solid fa-clock text-amber-500 text-sm"></i>
                        <span>Verification Pending</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Account Information Card -->
        <div class="rounded-3xl bg-white dark:bg-gradient-to-b dark:from-slate-900/90 dark:to-slate-950/90 border border-slate-200 dark:border-slate-800 p-6 sm:p-7 shadow-xl backdrop-blur-md">
            <div class="flex items-center gap-2.5 pb-4 mb-4 border-b border-slate-200 dark:border-slate-800">
                <i class="fa-solid fa-id-card text-sky-600 dark:text-cyan-400 text-lg"></i>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-wide">Account Details</h2>
            </div>

            @if(session('message'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-300 dark:border-emerald-400/30 text-emerald-800 dark:text-emerald-300 flex items-center gap-2.5 text-xs sm:text-sm font-semibold shadow-sm">
                    <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-base"></i>
                    <span>{{ session('message') }}</span>
                </div>
            @endif

            <form action="{{ route('profile_update') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 dark:focus:border-cyan-400 focus:outline-none transition" required minlength="4">
                    @error('name') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 dark:focus:border-cyan-400 focus:outline-none transition" required>
                    @error('email') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Preferred Ice Position</label>
                    <select id="playerDesiredRole" name="role" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 dark:focus:border-cyan-400 focus:outline-none cursor-pointer transition">
                        <option value="" disabled {{ old('role', Auth::user()->role_preference) ? '' : 'selected' }}>Select Position</option>
                        @foreach (App\Enums\Games\GameRoles::cases() as $roleOption)
                            <option value="{{ $roleOption->value }}" {{ old('role', Auth::user()->role_preference) == $roleOption->value ? 'selected' : '' }}>{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $roleOption->name)) }}</option>
                        @endforeach
                    </select>
                    @error('role') <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5">{{ $message }}</div> @enderror
                </div>

                <div class="flex flex-wrap gap-3 pt-3">
                    <button id="updateProfile" type="submit" class="px-5 py-2.5 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 dark:from-cyan-500 dark:to-sky-600 dark:hover:from-cyan-400 dark:hover:to-sky-500 text-white dark:text-slate-950 font-bold rounded-xl text-xs sm:text-sm transition shadow-md dark:shadow-[0_0_15px_rgba(56,189,248,0.3)]">Save Changes</button>
                    <button type="button" id="start-tour-btn" class="px-4 py-2.5 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80 transition text-xs sm:text-sm font-semibold">Restart App Tour</button>
                </div>
            </form>
        </div>

        <!-- Interface & Theme Preference Card -->
        <div id="theme-preference-card" class="rounded-3xl bg-white dark:bg-gradient-to-b dark:from-slate-900/90 dark:to-slate-950/90 border border-slate-200 dark:border-slate-800 p-6 sm:p-7 shadow-xl backdrop-blur-md"
            x-data="{
                activeTheme: localStorage.getItem('theme') === 'light' ? 'light' : 'dark',
                setTheme(theme) {
                    this.activeTheme = theme;
                    if (window.setTheme) {
                        window.setTheme(theme);
                    }
                }
            }"
            x-init="
                window.addEventListener('theme-changed', (e) => {
                    activeTheme = e.detail.theme;
                });
            ">
            <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-palette text-sky-600 dark:text-cyan-400 text-lg"></i>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-wide">Theme Preference</h2>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Choose your preferred visual appearance. Dark mode is active by default.</p>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hidden sm:inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full" :class="activeTheme === 'dark' ? 'bg-indigo-500' : 'bg-amber-500'"></span>
                    <span x-text="activeTheme === 'dark' ? 'Dark Mode Active' : 'Light Mode Active'"></span>
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Dark Mode Option (Default) -->
                <button type="button" @click="setTheme('dark')"
                    class="relative p-5 rounded-2xl border text-left transition-all duration-200 focus:outline-none flex flex-col justify-between group cursor-pointer"
                    :class="activeTheme === 'dark'
                        ? 'border-sky-500/80 dark:border-cyan-400 bg-sky-500/5 dark:bg-cyan-500/10 shadow-md ring-2 ring-sky-500/30 dark:ring-cyan-400/30'
                        : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/60 hover:border-slate-300 dark:hover:border-slate-700'">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-700 flex items-center justify-center text-indigo-400 shadow-sm">
                                <i class="fa-solid fa-moon text-lg"></i>
                            </div>
                            <span class="text-[10px] font-mono font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-700 dark:text-emerald-400"
                                x-show="activeTheme === 'dark'">
                                <i class="fa-solid fa-check mr-1"></i> Active
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-slate-900 dark:text-white">Dark Mode</span>
                            <span class="text-[10px] font-mono font-semibold px-2 py-0.5 rounded bg-slate-200/80 dark:bg-slate-800 text-slate-700 dark:text-slate-300">Default</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">High-contrast stadium dark palette designed for low-glare arena viewing.</p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-200/60 dark:border-slate-800 flex items-center gap-2 text-xs font-semibold text-sky-600 dark:text-cyan-400">
                        <span x-text="activeTheme === 'dark' ? 'Selected Preference' : 'Switch to Dark Mode'"></span>
                        <i class="fa-solid fa-arrow-right text-[10px]" x-show="activeTheme !== 'dark'"></i>
                    </div>
                </button>

                <!-- Light Mode Option -->
                <button type="button" @click="setTheme('light')"
                    class="relative p-5 rounded-2xl border text-left transition-all duration-200 focus:outline-none flex flex-col justify-between group cursor-pointer"
                    :class="activeTheme === 'light'
                        ? 'border-sky-500/80 dark:border-cyan-400 bg-sky-500/5 dark:bg-cyan-500/10 shadow-md ring-2 ring-sky-500/30 dark:ring-cyan-400/30'
                        : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/60 hover:border-slate-300 dark:hover:border-slate-700'">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center text-amber-500 shadow-sm">
                                <i class="fa-solid fa-sun text-lg"></i>
                            </div>
                            <span class="text-[10px] font-mono font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-700 dark:text-emerald-400"
                                x-show="activeTheme === 'light'">
                                <i class="fa-solid fa-check mr-1"></i> Active
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-slate-900 dark:text-white">Light Mode</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">Crisp off-white ice sheet palette with sharp dark typography and high readability.</p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-200/60 dark:border-slate-800 flex items-center gap-2 text-xs font-semibold text-sky-600 dark:text-cyan-400">
                        <span x-text="activeTheme === 'light' ? 'Selected Preference' : 'Switch to Light Mode'"></span>
                        <i class="fa-solid fa-arrow-right text-[10px]" x-show="activeTheme !== 'light'"></i>
                    </div>
                </button>
            </div>
        </div>

        <!-- Security & Password Card -->
        <div id="password-card" class="rounded-3xl bg-white dark:bg-gradient-to-b dark:from-slate-900/90 dark:to-slate-950/90 border border-slate-200 dark:border-slate-800 p-6 sm:p-7 shadow-xl backdrop-blur-md" x-data="passwordValidator()">
            <div class="flex items-center gap-2.5 pb-4 mb-4 border-b border-slate-200 dark:border-slate-800">
                <i class="fa-solid fa-shield-halved text-sky-600 dark:text-cyan-400 text-lg"></i>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-wide">Security & Password</h2>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Ensure your account is protected with a strong, updated password.</p>
                </div>
            </div>

            @if(session('password_status'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-300 dark:border-emerald-400/30 text-emerald-800 dark:text-emerald-300 flex items-center gap-2.5 text-xs sm:text-sm font-semibold shadow-sm animate-fade-in">
                    <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-base"></i>
                    <span>{{ session('password_status') }}</span>
                </div>
            @endif

            <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Current Password -->
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Current Password</label>
                    <div class="relative">
                        <input
                            :type="showCurrent ? 'text' : 'password'"
                            name="current_password"
                            required
                            placeholder="••••••••"
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl pl-4 pr-10 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 dark:focus:border-cyan-400 focus:outline-none transition @error('current_password') border-rose-500 @enderror"
                        >
                        <button
                            type="button"
                            @click="showCurrent = !showCurrent"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-white focus:outline-none"
                            tabindex="-1"
                        >
                            <i :class="showCurrent ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">New Password</label>
                    <div class="relative">
                        <input
                            :type="showNew ? 'text' : 'password'"
                            name="password"
                            x-model="password"
                            required
                            minlength="8"
                            placeholder="At least 8 characters"
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl pl-4 pr-10 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 dark:focus:border-cyan-400 focus:outline-none transition @error('password') border-rose-500 @enderror"
                        >
                        <button
                            type="button"
                            @click="showNew = !showNew"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-white focus:outline-none"
                            tabindex="-1"
                        >
                            <i :class="showNew ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror

                    <!-- Password Strength Meter -->
                    <div class="mt-2.5 space-y-2 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-xl p-3" x-show="password.length > 0" x-transition>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-600 dark:text-slate-400 font-mono">Password Strength:</span>
                            <span class="font-bold font-mono" :class="strengthTextColor" x-text="strengthLabel"></span>
                        </div>
                        <div class="grid grid-cols-4 gap-1.5 h-1.5 w-full bg-slate-200 dark:bg-slate-900 rounded-full overflow-hidden p-0.5">
                            <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 1 ? strengthBgColor : 'bg-transparent'"></div>
                            <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 2 ? strengthBgColor : 'bg-transparent'"></div>
                            <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 3 ? strengthBgColor : 'bg-transparent'"></div>
                            <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 4 ? strengthBgColor : 'bg-transparent'"></div>
                        </div>

                        <!-- Requirements Checklist -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1 text-xs">
                            <div class="flex items-center gap-1.5" :class="hasMinLength ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500'">
                                <i :class="hasMinLength ? 'fa-solid fa-check-circle' : 'fa-regular fa-circle'"></i>
                                <span>8+ characters</span>
                            </div>
                            <div class="flex items-center gap-1.5" :class="hasLetter ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500'">
                                <i :class="hasLetter ? 'fa-solid fa-check-circle' : 'fa-regular fa-circle'"></i>
                                <span>Letters</span>
                            </div>
                            <div class="flex items-center gap-1.5" :class="hasNumberOrSpecial ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500'">
                                <i :class="hasNumberOrSpecial ? 'fa-solid fa-check-circle' : 'fa-regular fa-circle'"></i>
                                <span>Numbers or symbols</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Confirm New Password</label>
                    <div class="relative">
                        <input
                            :type="showConfirm ? 'text' : 'password'"
                            name="password_confirmation"
                            x-model="confirmation"
                            required
                            placeholder="Repeat new password"
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl pl-4 pr-10 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 dark:focus:border-cyan-400 focus:outline-none transition"
                        >
                        <button
                            type="button"
                            @click="showConfirm = !showConfirm"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-white focus:outline-none"
                            tabindex="-1"
                        >
                            <i :class="showConfirm ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                        </button>
                    </div>

                    <!-- Live Match Indicator -->
                    <div class="mt-2 text-xs font-mono" x-show="confirmation.length > 0" x-transition>
                        <span x-show="passwordsMatch" class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                            <i class="fa-solid fa-check"></i> Passwords match
                        </span>
                        <span x-show="!passwordsMatch" class="text-amber-600 dark:text-amber-400 flex items-center gap-1">
                            <i class="fa-solid fa-triangle-exclamation"></i> Passwords do not match yet
                        </span>
                    </div>
                </div>

                <div class="pt-3">
                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 dark:from-cyan-500 dark:to-sky-600 dark:hover:from-cyan-400 dark:hover:to-sky-500 text-white dark:text-slate-950 font-bold rounded-xl text-xs sm:text-sm transition shadow-md dark:shadow-[0_0_15px_rgba(56,189,248,0.3)]"
                    >
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            var btn = document.getElementById('start-tour-btn');
            if (!btn) return;
            btn.addEventListener('click', function(){
                // Restart onboarding flow from the dashboard
                window.location.href = '/home?onboarding=1&restart=1';
            });
        });

        function passwordValidator() {
            return {
                showCurrent: false,
                showNew: false,
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