@extends('layouts.app')

@section('content')

<div class="parent-container d-flex align-items-center justify-content-center h-100">
    <div x-data='{"tab": @json(old("form", "login")) }' class="w-full max-w-3xl grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="hidden md:flex flex-col justify-center bg-slate-800 p-8 rounded-lg">
            <h3 class="text-2xl font-semibold text-ice-blue mb-2">Pickup Puck</h3>
            <p class="text-slate-300">Pickup hockey scheduling made simple — manage games, players and payments.</p>
        </div>

        <div class="bg-slate-900 border border-slate-700 rounded-lg shadow-md px-6 py-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <button @click="tab = 'login'" :class="tab === 'login' ? 'bg-ice-blue text-deep-navy' : 'text-slate-300'" class="px-3 py-1 rounded font-medium">Login</button>
                    <button @click="tab = 'register'" :class="tab === 'register' ? 'bg-ice-blue text-deep-navy' : 'text-slate-300'" class="px-3 py-1 rounded font-medium">Register</button>
                </div>
            </div>

            <!-- Login Form -->
            <form x-show="tab === 'login'" x-cloak method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showLoginPassword: false }">
                @csrf
                <input type="hidden" name="form" value="login">

                <div>
                    <label for="email" class="block text-sm text-gray-100">{{ __('Email Address') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        class="w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
                    @if(old('form') === 'login')
                        @error('email') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                    @endif
                </div>

                <div>
                    <label for="password" class="block text-sm text-gray-100">{{ __('Password') }}</label>
                    <div class="relative">
                        <input id="password" type="password" :type="showLoginPassword ? 'text' : 'password'" name="password" required autocomplete="current-password"
                            class="w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-ice-blue" />

                        <button type="button" @click="showLoginPassword = !showLoginPassword" class="absolute inset-y-0 right-3 flex items-center text-slate-200 hover:text-white focus:outline-none">
                            <i x-show="!showLoginPassword" x-cloak class="fa-regular fa-eye"></i>
                            <i x-show="showLoginPassword" x-cloak class="fa-regular fa-eye-slash"></i>
                        </button>
                    </div>
                    @if(old('form') === 'login')
                        @error('password') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-slate-300">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} class="h-4 w-4 text-ice-blue bg-slate-800 border-slate-600 rounded">
                        {{ __('Remember Me') }}
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-sm text-slate-300 hover:text-white" href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
                    @endif
                </div>

                <div>
                    <button type="submit" class="w-full bg-ice-blue text-deep-navy font-semibold py-2 rounded shadow">{{ __('Login') }}</button>
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
                    <label for="name" class="block text-sm text-gray-100">{{ __('Name') }}</label>
                    <input id="name" type="text" name="name" x-model="name" @blur="onNameBlur()" required autocomplete="off" autocapitalize="words" spellcheck="false"
                        class="w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
                    @if(old('form') === 'register' || session('_old_input.form') === 'register')
                        @error('name') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                    @endif
                    <div x-show="nameBlurred && (name || '').trim().length > 0 && !nameIsValid()" x-cloak class="text-red-400 text-sm mt-1">Please enter first and last name</div>
                </div>

                <div>
                    <label for="reg_email" class="block text-sm text-gray-100">{{ __('Email Address') }}</label>
                    <input id="reg_email" type="email" name="email" x-model="regEmail" @input="onEmailInput()" @blur="checkEmail()" required autocomplete="email" autocapitalize="none"
                        class="w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
                    @if(old('form') === 'register' || session('_old_input.form') === 'register')
                        @error('email') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                    @endif
                    <div x-show="emailChecking" x-cloak class="text-slate-300 text-sm mt-1">Checking…</div>
                    <div x-show="emailChecked && emailExists" x-cloak class="text-red-400 text-sm mt-1">That email is already in use — please <a href="{{ route('login') }}" class="underline">login</a>.</div>
                </div>

                <div>
                    <label for="reg_password" class="block text-sm text-gray-100">{{ __('Password') }}</label>
                    <div class="relative">
                        <input id="reg_password" type="password" :type="showPassword ? 'text' : 'password'" name="password" x-model="password" @blur="passwordTouched = true" @input="passwordTouched = true" required autocomplete="new-password"
                            class="w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-ice-blue" />

                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-3 flex items-center text-slate-200 hover:text-white focus:outline-none">
                            <i x-show="!showPassword" x-cloak class="fa-regular fa-eye"></i>
                            <i x-show="showPassword" x-cloak class="fa-regular fa-eye-slash"></i>
                        </button>
                    </div>
                    @if(old('form') === 'register' || session('_old_input.form') === 'register')
                        @error('password') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                    @endif
                    <div x-show="passwordTouched && password.length < 8" x-cloak class="text-red-400 text-sm mt-1">Password must be at least 8 characters.</div>
                </div>

                <div>
                    <label for="password-confirm" class="block text-sm text-gray-100">{{ __('Confirm Password') }}</label>
                    <div class="relative">
                        <input id="password-confirm" type="password" :type="showPasswordConfirmation ? 'text' : 'password'" name="password_confirmation" x-model="password_confirmation" @input="passwordTouched = true" required autocomplete="new-password"
                            class="w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-ice-blue" />

                        <button type="button" @click="showPasswordConfirmation = !showPasswordConfirmation" class="absolute inset-y-0 right-3 flex items-center text-slate-200 hover:text-white focus:outline-none">
                            <i x-show="!showPasswordConfirmation" x-cloak class="fa-regular fa-eye"></i>
                            <i x-show="showPasswordConfirmation" x-cloak class="fa-regular fa-eye-slash"></i>
                        </button>
                    </div>
                    <div x-show="password_confirmation.length > 0" x-cloak
                         :class="password === password_confirmation ? 'text-green-400' : 'text-red-400'"
                         class="text-sm mt-1"
                         x-text="password === password_confirmation ? 'Passwords match' : 'Passwords do not match'"></div>
                </div>

                <div>
                    <button type="submit" class="w-full bg-ice-blue text-deep-navy font-semibold py-2 rounded shadow">{{ __('Register') }}</button>
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
