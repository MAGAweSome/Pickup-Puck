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
            <form x-show="tab === 'login'" x-cloak method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="form" value="login">

                <div>
                    <label for="email" class="block text-sm text-gray-100">{{ __('Email Address') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
                    @if(old('form') === 'login')
                        @error('email') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                    @endif
                </div>

                <div>
                    <label for="password" class="block text-sm text-gray-100">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
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
            <form x-show="tab === 'register'" x-cloak method="POST" action="{{ route('register') }}" class="space-y-4" autocomplete="off"
                data-old-name="{{ old('name', '') }}" data-old-email="{{ old('email', '') }}" data-old-form="{{ old('form', '') }}"
                x-data="{
                      name: '',
                      password: '',
                      password_confirmation: '',
                      nameTouched: false,
                      passwordTouched: false,
                      emailChecking: false,
                      emailExists: false,
                      nameIsValid() {
                          const parts = this.name.trim().split(/\s+/).filter(Boolean);
                          return parts.length >= 2 && parts[parts.length - 1].length >= 2;
                      },
                      toTitleCase(s) {
                          return s.toLowerCase().split(/\s+/).filter(Boolean).map(seg => {
                              return seg.split(/([\'\-])/).map(part => {
                                  return part.length > 0 ? part.charAt(0).toUpperCase() + part.slice(1) : part;
                              }).join('');
                          }).join(' ');
                      },
                      onNameBlur() {
                          this.name = this.toTitleCase(this.name.trim());
                          this.nameTouched = true;
                      }
                      ,
                      async checkEmail(emailVal) {
                          // If called without param, try to read from the input
                          if (typeof emailVal === 'undefined') {
                              const el = document.getElementById('reg_email');
                              emailVal = el ? el.value.trim() : '';
                          } else {
                              emailVal = emailVal ? String(emailVal).trim() : '';
                          }
                          if (!emailVal || !/.+@.+\..+/.test(emailVal)) {
                              this.emailExists = false;
                              return;
                          }
                          this.emailChecking = true;
                          try {
                              const res = await fetch("{{ route('register.check_email') }}", {
                                  method: 'POST',
                                  headers: {
                                      'Content-Type': 'application/json',
                                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                  },
                                  credentials: 'same-origin',
                                  body: JSON.stringify({ email: emailVal })
                              });
                              if (res.ok) {
                                  const data = await res.json();
                                      this.emailExists = !!data.exists;
                              }
                          } catch (e) {
                              this.emailExists = false;
                          } finally {
                              this.emailChecking = false;
                          }
                      }
                  }"
                  x-init="(function(){
                      emailExists = false;
                      try {
                          if ($el.dataset.oldForm === 'register') {
                              if ($el.dataset.oldName) { name = $el.dataset.oldName; onNameBlur(); }
                              if ($el.dataset.oldEmail) { regEmail = $el.dataset.oldEmail; }
                          } else {
                              name = '';
                              regEmail = '';
                              password = '';
                              password_confirmation = '';
                              const p = document.getElementById('reg_password'); if (p) p.value = '';
                              const pc = document.getElementById('password-confirm'); if (pc) pc.value = '';
                              const e = document.getElementById('reg_email'); if (e) e.value = '';
                          }
                      } catch (e) {
                          // ignore
                      }
                  })()"
                  @submit="name = toTitleCase(name.trim())"
                  >
                @csrf
                <!-- Hidden fields to capture browser autofill (keeps passwords out of visible inputs) -->
                <div style="position: absolute; left: -9999px; top: -9999px;" aria-hidden="true">
                    <input type="text" name="fake_username" autocomplete="username" tabindex="-1">
                    <input type="password" name="fake_password" autocomplete="current-password" tabindex="-1">
                </div>
                <input type="hidden" name="form" value="register">

                <div>
                    <label for="name" class="block text-sm text-gray-100">{{ __('Name') }}</label>
                    <input id="name" type="text" name="name" x-model="name" @blur="onNameBlur()" required autocomplete="name"
                        class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
                    @if(old('form') === 'register')
                        @error('name') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                    @endif
                    <div x-show="nameTouched && !nameIsValid()" x-cloak class="text-red-400 text-sm mt-1">Please enter your full name (first and last), and make sure the last name is at least 2 letters.</div>
                </div>

                <div>
                    <label for="reg_email" class="block text-sm text-gray-100">{{ __('Email Address') }}</label>
                    <input id="reg_email" type="email" name="email" x-model="regEmail" @blur="checkEmail($event.target.value)" required autocomplete="email"
                        class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
                    @if(old('form') === 'register')
                        @error('email') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                    @endif
                    <div x-show="emailChecking" x-cloak class="text-slate-300 text-sm mt-1">Checking…</div>
                    <div x-show="emailExists" x-cloak class="text-red-400 text-sm mt-1">That email is already in use — please <a href="{{ route('login') }}" class="underline">login</a>.</div>
                </div>

                <div>
                    <label for="reg_password" class="block text-sm text-gray-100">{{ __('Password') }}</label>
                    <input id="reg_password" type="password" name="password" x-model="password" @blur="passwordTouched = true" required autocomplete="new-password"
                        class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
                    @if(old('form') === 'register')
                        @error('password') <div class="text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                    @endif
                    <div x-show="passwordTouched && password.length < 8" x-cloak class="text-red-400 text-sm mt-1">Password must be at least 8 characters.</div>
                </div>

                <div>
                    <label for="password-confirm" class="block text-sm text-gray-100">{{ __('Confirm Password') }}</label>
                    <input id="password-confirm" type="password" name="password_confirmation" x-model="password_confirmation" required autocomplete="new-password"
                        class="mt-1 w-full bg-slate-800 text-gray-100 border border-slate-700 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ice-blue" />
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
