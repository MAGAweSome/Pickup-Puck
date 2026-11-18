@extends('layouts.app')

@section('content')

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0 w-full text-center sm:text-left">
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-slate-100">
                    {{ $user->name }}@if(substr($user->name, -1) != 's')'s @endif Profile
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    <span>Admin view of player account settings.</span>
                    <span class="font-mono text-slate-500 dark:text-slate-400">User #{{ $user->id }} · {{ $user->email }}</span>
                </p>
            </div>

            <div class="flex w-full flex-wrap items-center gap-2 sm:w-auto sm:justify-end">
                <a href="{{ route('user_list') }}" class="inline-flex flex-1 sm:flex-none items-center justify-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition no-underline shadow-sm">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>All Players</span>
                </a>
                <a href="{{ route('user_game_history', ['user' => $user->id]) }}" class="inline-flex flex-1 sm:flex-none items-center justify-center gap-1.5 px-3.5 py-2 bg-sky-50 hover:bg-sky-100 dark:bg-sky-500/15 dark:hover:bg-sky-500/25 border border-sky-200 dark:border-sky-500/30 text-sky-700 dark:text-sky-300 rounded-xl text-xs font-bold transition no-underline shadow-sm">
                    <i class="fa-solid fa-clock-rotate-left text-[10px]"></i>
                    <span>Game History</span>
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-sm font-semibold flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-500/15 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-sm font-semibold flex items-center gap-2.5">
                <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700/80 rounded-3xl p-6 sm:p-8 shadow-sm dark:shadow-md">
            <div class="flex items-center gap-2.5 pb-4 mb-6 border-b border-slate-100 dark:border-slate-800">
                <i class="fa-solid fa-id-card text-sky-600 dark:text-sky-400"></i>
                <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Account Information</h2>
            </div>

            <form action="{{ route('user_role_update.user_id', ['user' => $user->id]) }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        minlength="4"
                        class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 transition shadow-inner"
                    />
                    @error('name')
                        <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 font-medium">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 transition shadow-inner"
                    />
                    @error('email')
                        <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 font-medium">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="gameRole" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Preferred Position</label>
                    <select
                        id="gameRole"
                        name="gameRole"
                        class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 transition shadow-inner"
                    >
                        <option value="" {{ old('gameRole', $user->role_preference) ? '' : 'selected' }}>Select Position</option>
                        @foreach ($GAME_ROLES as $gamerole)
                            <option value="{{ $gamerole->value }}" {{ old('gameRole', $user->role_preference) === $gamerole->value ? 'selected' : '' }}>
                                {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $gamerole->name)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('gameRole')
                        <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 font-medium">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="level" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Player Level</label>
                    <select
                        id="level"
                        name="level"
                        class="w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 transition shadow-inner"
                    >
                        @php
                            $levelDescriptions = [
                                1 => 'Beginner / low rec',
                                2 => 'Recreational',
                                3 => 'Intermediate / competitive',
                                4 => 'Advanced / high skill',
                            ];
                        @endphp
                        @for ($i = 1; $i <= 4; $i++)
                            <option value="{{ $i }}" {{ (int) old('level', $user->level ?? 3) === $i ? 'selected' : '' }}>{{ $i }} - {{ $levelDescriptions[$i] }}</option>
                        @endfor
                    </select>
                    @error('level')
                        <div class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 font-medium">{{ $message }}</div>
                    @enderror
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-3 select-none cursor-pointer">
                        <input
                            type="checkbox"
                            value="1"
                            id="adminCheck"
                            name="adminCheck"
                            class="h-5 w-5 rounded border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-sky-600 focus:ring-sky-500"
                            @if (old('adminCheck', $user->hasRole('admin') ? '1' : null)) checked @endif
                        />
                        <span class="text-sm font-bold text-slate-900 dark:text-slate-100">Admin</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">(Grants Admin Privileges)</span>
                    </label>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 dark:from-sky-500 dark:to-cyan-500 text-white dark:text-slate-950 font-bold rounded-xl text-sm shadow-md transition"
                        type="submit"
                    >
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Save Changes</span>
                    </button>

                    <a href="{{ route('user_list') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition no-underline">Cancel</a>
                </div>
            </form>
        </div>

        <!-- Secure 5-Hour Self-Service Account & Password Recovery Link -->
        <div
            class="bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-cyan-500/30 rounded-3xl p-6 sm:p-8 shadow-sm dark:shadow-md space-y-4"
            x-data="{
                recoveryUrl: @js(session('recovery_link', '')),
                copied: false,
                loadingLink: false,
                loadingEmail: false,
                statusMsg: '',
                errorMsg: '',
                async generateLink() {
                    this.loadingLink = true;
                    this.errorMsg = '';
                    this.statusMsg = '';
                    try {
                        const res = await fetch('{{ route('admin.user.recovery_link', ['user' => $user->id]) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({})
                        });
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Failed to generate link.');
                        this.recoveryUrl = data.url;
                        this.statusMsg = '5-hour single-use link generated.';
                    } catch (e) {
                        this.errorMsg = e.message;
                    } finally {
                        this.loadingLink = false;
                    }
                },
                async copyLink() {
                    if (!this.recoveryUrl) return;
                    try {
                        await navigator.clipboard.writeText(this.recoveryUrl);
                    } catch (e) {
                        const el = this.$refs.urlInput;
                        el.select();
                        document.execCommand('copy');
                    }
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2500);
                },
                async sendEmail() {
                    this.loadingEmail = true;
                    this.errorMsg = '';
                    this.statusMsg = '';
                    try {
                        const res = await fetch('{{ route('admin.user.recovery_email', ['user' => $user->id]) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ recovery_url: this.recoveryUrl })
                        });
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Failed to send email.');
                        if (data.url) this.recoveryUrl = data.url;
                        this.statusMsg = data.message || 'Recovery email dispatched.';
                    } catch (e) {
                        this.errorMsg = e.message;
                    } finally {
                        this.loadingEmail = false;
                    }
                }
            }"
        >
            <div class="flex items-center justify-between gap-2 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-key text-cyan-500"></i>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Player Account &amp; Password Recovery Link</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Admins cannot edit passwords directly. Issue a secure 5-hour single-use link for {{ $user->name }}.</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <button
                    type="button"
                    @click="generateLink()"
                    :disabled="loadingLink"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-cyan-500 dark:hover:bg-cyan-400 text-white dark:text-slate-950 text-xs font-bold transition shadow-sm cursor-pointer disabled:opacity-60"
                >
                    <i class="fa-solid fa-link"></i>
                    <span x-text="loadingLink ? 'Generating...' : (recoveryUrl ? 'Regenerate 5-Hour Link' : 'Generate 5-Hour Link')"></span>
                </button>

                <button
                    type="button"
                    @click="sendEmail()"
                    :disabled="loadingEmail"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 dark:bg-sky-500/15 dark:hover:bg-sky-500/25 border border-sky-200 dark:border-sky-500/30 text-sky-700 dark:text-sky-300 text-xs font-bold transition shadow-sm cursor-pointer disabled:opacity-60"
                >
                    <i class="fa-solid fa-envelope"></i>
                    <span x-text="loadingEmail ? 'Sending Email...' : 'Send Link via Email'"></span>
                </button>
            </div>

            <div x-show="recoveryUrl" class="space-y-2" style="display: none;">
                <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    5-Hour Signed Self-Service URL
                </label>
                <div class="flex items-center gap-2">
                    <input
                        x-ref="urlInput"
                        type="text"
                        readonly
                        :value="recoveryUrl"
                        class="flex-1 bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs font-mono text-slate-700 dark:text-cyan-300 select-all focus:outline-none"
                    >
                    <button
                        type="button"
                        @click="copyLink()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition cursor-pointer shrink-0"
                    >
                        <i class="fa-solid" :class="copied ? 'fa-check text-emerald-400' : 'fa-copy'"></i>
                        <span x-text="copied ? 'Copied!' : 'Copy to Clipboard'"></span>
                    </button>
                </div>
            </div>

            <p x-show="statusMsg" x-text="statusMsg" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400" style="display: none;"></p>
            <p x-show="errorMsg" x-text="errorMsg" class="text-xs font-semibold text-rose-600 dark:text-rose-400" style="display: none;"></p>
        </div>

        <!-- Danger Zone: Permanent Player Deletion -->
        @if ($user->id !== auth()->id())
            <div
                class="bg-white dark:bg-[#1e293b] border border-rose-200 dark:border-rose-500/30 rounded-3xl p-6 sm:p-8 shadow-sm dark:shadow-md space-y-4"
                x-data="{ confirmDelete: false }"
            >
                <div class="flex items-center gap-2.5 pb-3 border-b border-rose-100 dark:border-rose-500/20">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                    <div>
                        <h2 class="text-base font-bold text-rose-700 dark:text-rose-400">Danger Zone</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Permanently remove this inactive player and clean up all game signups and roster entries.</p>
                    </div>
                </div>

                <div x-show="!confirmDelete">
                    <button
                        type="button"
                        @click="confirmDelete = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold transition shadow-sm cursor-pointer"
                    >
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Delete Player Permanently</span>
                    </button>
                </div>

                <div x-show="confirmDelete" class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-500/40 space-y-3" style="display: none;">
                    <p class="text-xs sm:text-sm font-semibold text-rose-800 dark:text-rose-200">
                        Are you absolutely sure you want to permanently delete <strong>{{ $user->name }}</strong>? This action cannot be undone.
                    </p>
                    <div class="flex items-center gap-2.5">
                        <form method="POST" action="{{ route('admin.user.destroy', ['user' => $user->id]) }}">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-black transition cursor-pointer"
                            >
                                <i class="fa-solid fa-trash-can"></i>
                                <span>Yes, Delete Permanently</span>
                            </button>
                        </form>
                        <button
                            type="button"
                            @click="confirmDelete = false"
                            class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition cursor-pointer"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
