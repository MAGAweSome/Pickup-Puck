@extends('layouts.app')

@section('content')

    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <h1 class="text-3xl font-bold">Profile</h1>
            <p class="mt-2 text-slate-300">Update your account information, role preferences, and password security.</p>
        </div>

        <!-- Account Information Card -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6 shadow-md">
            <h2 class="text-xl font-semibold text-ice mb-4 flex items-center gap-2">
                <i class="fa-regular fa-user text-ice-blue"></i>
                <span>Account Information</span>
            </h2>

            @if(session('message'))
                <div class="mb-4 p-3 rounded-lg bg-emerald-600/90 text-white flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('message') }}</span>
                </div>
            @endif

            <form action="{{ route('profile_update') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-slate-300 text-sm font-medium">Name</label>
                    <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" class="mt-1 w-full bg-slate-900 border border-slate-700 rounded-md px-3.5 py-2 text-ice focus:border-ice-blue focus:ring-1 focus:ring-ice-blue outline-none transition" required minlength="4">
                    @error('name') <div class="text-rose-400 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-slate-300 text-sm font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="mt-1 w-full bg-slate-900 border border-slate-700 rounded-md px-3.5 py-2 text-ice focus:border-ice-blue focus:ring-1 focus:ring-ice-blue outline-none transition" required>
                    @error('email') <div class="text-rose-400 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-slate-300 text-sm font-medium">Preferred Position</label>
                    <select id="playerDesiredRole" name="role" class="mt-1 w-full bg-slate-900 border border-slate-700 rounded-md px-3.5 py-2 text-ice focus:border-ice-blue focus:ring-1 focus:ring-ice-blue outline-none transition">
                        <option value="" disabled {{ old('role', Auth::user()->role_preference) ? '' : 'selected' }}>Select Position</option>
                        @foreach (App\Enums\Games\GameRoles::cases() as $roleOption)
                            <option value="{{ $roleOption->value }}" {{ old('role', Auth::user()->role_preference) == $roleOption->value ? 'selected' : '' }}>{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $roleOption->name)) }}</option>
                        @endforeach
                    </select>
                    @error('role') <div class="text-rose-400 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button id="updateProfile" type="submit" class="px-4 py-2 bg-ice-blue text-deep-navy font-semibold rounded-md hover:bg-ice transition shadow-sm">Update Profile</button>
                    <button type="button" id="start-tour-btn" class="px-4 py-2 border border-slate-600 rounded-md text-ice hover:bg-slate-700/60 transition">Start Tour</button>
                </div>
            </form>
        </div>

        <!-- Security & Password Card -->
        <div id="password-card" class="bg-slate-800 border border-slate-700 rounded-lg p-6 shadow-md" x-data="passwordValidator()">
            <h2 class="text-xl font-semibold text-ice mb-1 flex items-center gap-2">
                <i class="fa-solid fa-lock text-ice-blue"></i>
                <span>Security & Password</span>
            </h2>
            <p class="text-sm text-slate-400 mb-4">Ensure your account is protected with a strong, updated password.</p>

            @if(session('password_status'))
                <div class="mb-4 p-3.5 rounded-lg bg-emerald-600/90 text-white flex items-center gap-2 shadow-sm animate-fade-in">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="font-medium">{{ session('password_status') }}</span>
                </div>
            @endif

            <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Current Password -->
                <div>
                    <label class="block text-slate-300 text-sm font-medium">Current Password</label>
                    <div class="relative mt-1">
                        <input
                            :type="showCurrent ? 'text' : 'password'"
                            name="current_password"
                            required
                            placeholder="••••••••"
                            class="w-full bg-slate-900 border border-slate-700 rounded-md pl-3.5 pr-10 py-2 text-ice focus:border-ice-blue focus:ring-1 focus:ring-ice-blue outline-none transition @error('current_password') border-rose-500 @enderror"
                        >
                        <button
                            type="button"
                            @click="showCurrent = !showCurrent"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-ice focus:outline-none"
                            tabindex="-1"
                        >
                            <i :class="showCurrent ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <div class="text-rose-400 text-xs mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-slate-300 text-sm font-medium">New Password</label>
                    <div class="relative mt-1">
                        <input
                            :type="showNew ? 'text' : 'password'"
                            name="password"
                            x-model="password"
                            required
                            minlength="8"
                            placeholder="At least 8 characters"
                            class="w-full bg-slate-900 border border-slate-700 rounded-md pl-3.5 pr-10 py-2 text-ice focus:border-ice-blue focus:ring-1 focus:ring-ice-blue outline-none transition @error('password') border-rose-500 @enderror"
                        >
                        <button
                            type="button"
                            @click="showNew = !showNew"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-ice focus:outline-none"
                            tabindex="-1"
                        >
                            <i :class="showNew ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="text-rose-400 text-xs mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror

                    <!-- Password Strength Meter -->
                    <div class="mt-2 space-y-1.5" x-show="password.length > 0" x-transition>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400">Strength:</span>
                            <span class="font-semibold" :class="strengthTextColor" x-text="strengthLabel"></span>
                        </div>
                        <div class="grid grid-cols-4 gap-1.5 h-1.5 w-full bg-slate-900 rounded-full overflow-hidden p-0.5">
                            <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 1 ? strengthBgColor : 'bg-transparent'"></div>
                            <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 2 ? strengthBgColor : 'bg-transparent'"></div>
                            <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 3 ? strengthBgColor : 'bg-transparent'"></div>
                            <div class="h-full rounded-full transition-all duration-300" :class="strengthScore >= 4 ? strengthBgColor : 'bg-transparent'"></div>
                        </div>

                        <!-- Requirements Checklist -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1 text-xs">
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
                                <span>Numbers or symbols</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-slate-300 text-sm font-medium">Confirm New Password</label>
                    <div class="relative mt-1">
                        <input
                            :type="showConfirm ? 'text' : 'password'"
                            name="password_confirmation"
                            x-model="confirmation"
                            required
                            placeholder="Repeat new password"
                            class="w-full bg-slate-900 border border-slate-700 rounded-md pl-3.5 pr-10 py-2 text-ice focus:border-ice-blue focus:ring-1 focus:ring-ice-blue outline-none transition"
                        >
                        <button
                            type="button"
                            @click="showConfirm = !showConfirm"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-ice focus:outline-none"
                            tabindex="-1"
                        >
                            <i :class="showConfirm ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                        </button>
                    </div>

                    <!-- Live Match Indicator -->
                    <div class="mt-1 text-xs" x-show="confirmation.length > 0" x-transition>
                        <span x-show="passwordsMatch" class="text-emerald-400 flex items-center gap-1">
                            <i class="fa-solid fa-check"></i> Passwords match
                        </span>
                        <span x-show="!passwordsMatch" class="text-amber-400 flex items-center gap-1">
                            <i class="fa-solid fa-triangle-exclamation"></i> Passwords do not match yet
                        </span>
                    </div>
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        class="px-5 py-2 bg-ice-blue text-deep-navy font-semibold rounded-md hover:bg-ice transition shadow-sm"
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