<!-- Connected Profiles & Accounts Hub -->

@if(Auth::user()->isManaged())
    <!-- Notice for Managed Accounts: Cannot manage other accounts -->
    <div class="rounded-3xl bg-white dark:bg-gradient-to-b dark:from-slate-900/90 dark:to-slate-950/90 border border-slate-200 dark:border-slate-800 p-6 sm:p-7 shadow-xl max-w-4xl mx-auto w-full">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-500/15 border border-sky-500/30 flex items-center justify-center text-sky-600 dark:text-cyan-400 shrink-0">
                <i class="fa-solid fa-link text-xl"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Connected Account</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                    This profile is connected to and managed by <strong class="text-slate-900 dark:text-white">{{ Auth::user()->manager->name ?? 'Account Manager' }}</strong>. 
                    Connected accounts cannot manage other accounts. To unlink and manage this account independently, ask your account manager to assign your login credentials from their dashboard.
                </p>
            </div>
        </div>
    </div>
@else
    <!-- Full Hub for Primary Account Managers -->
    <div id="connected-accounts-card" class="rounded-3xl bg-white dark:bg-gradient-to-b dark:from-slate-900/90 dark:to-slate-950/90 border border-slate-200 dark:border-slate-800 p-6 sm:p-7 shadow-xl backdrop-blur-md max-w-4xl mx-auto w-full"
        x-data="{
            showCreateModal: false,
            showClaimModal: false,
            showEditModal: false,
            showPromoteModal: false,
            showDeleteModal: false,
            selectedProfile: null,
            openEdit(profile) {
                this.selectedProfile = profile;
                this.showEditModal = true;
            },
            openPromote(profile) {
                this.selectedProfile = profile;
                this.showPromoteModal = true;
            },
            openDelete(profile) {
                this.selectedProfile = profile;
                this.showDeleteModal = true;
            }
        }">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 mb-5 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-sky-500/15 border border-sky-500/30 flex items-center justify-center text-sky-600 dark:text-cyan-400">
                    <i class="fa-solid fa-users-gear text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-wide">Connected Profiles</h2>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Manage game signups, positions, and skill ratings for family, teammates, or friends under your account.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" @click="showClaimModal = true" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-user-plus text-xs text-sky-500"></i>
                    <span>Take Control of a Guest</span>
                </button>
                <button type="button" @click="showCreateModal = true" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 dark:from-cyan-500 dark:to-sky-600 dark:hover:from-cyan-400 dark:hover:to-sky-500 text-white dark:text-slate-950 font-bold text-xs transition shadow-md flex items-center gap-1.5">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>New Connection</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-300 dark:border-emerald-400/30 text-emerald-800 dark:text-emerald-300 flex items-center gap-2.5 text-xs sm:text-sm font-semibold shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 p-3.5 rounded-xl bg-rose-50 dark:bg-rose-500/15 border border-rose-300 dark:border-rose-400/30 text-rose-800 dark:text-rose-300 text-xs sm:text-sm font-medium space-y-1">
                @foreach ($errors->all() as $err)
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-xs"></i>
                        <span>{{ $err }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Connected Profiles List -->
        @if($managedProfiles->isEmpty())
            <div class="p-8 text-center rounded-2xl bg-slate-50/70 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800">
                <div class="w-12 h-12 rounded-2xl bg-slate-200 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400 text-xl">
                    <i class="fa-solid fa-users"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">No Connected Profiles Yet</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400 max-w-md mx-auto mt-1 leading-relaxed">
                    You can manage RSVPs, positions, and skill ratings for friends, teammates, or family under your account. Add a new profile or claim a past guest above!
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($managedProfiles as $p)
                    <div class="p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-950/60 flex flex-col justify-between gap-4 shadow-sm hover:border-slate-300 dark:hover:border-slate-700 transition">
                        <div>
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-500 to-cyan-400 text-slate-950 font-black text-sm flex items-center justify-center shadow-sm">
                                        {{ strtoupper(substr($p->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-sm font-black text-slate-900 dark:text-white">{{ $p->name }}</h4>
                                            @if($p->jersey_number)
                                                <span class="text-[11px] font-mono font-bold text-sky-600 dark:text-cyan-400 bg-sky-50 dark:bg-sky-500/10 border border-sky-200 dark:border-sky-500/20 px-1.5 py-0.5 rounded">#{{ $p->jersey_number }}</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] font-mono font-bold uppercase tracking-wider px-2 py-0.5 rounded {{ $p->role_preference === 'goalie' ? 'bg-amber-500/15 text-amber-700 dark:text-amber-400 border border-amber-500/30' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                                                {{ ucfirst($p->role_preference ?? 'Skater') }}
                                            </span>
                                            <span class="text-[10px] font-mono font-semibold text-slate-500 dark:text-slate-400">
                                                Skill Level {{ $p->level ?? 3 }}/5
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="pt-3 border-t border-slate-200/60 dark:border-slate-800/80 flex flex-wrap items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-1.5">
                                <button type="button" 
                                    @click='openEdit({ id: {{ $p->id }}, name: @json($p->name), role_preference: @json($p->role_preference ?? "player"), jersey_number: {{ $p->jersey_number ?? "null" }}, level: {{ $p->level ?? 3 }} })'
                                    class="px-2.5 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition font-semibold text-xs flex items-center gap-1">
                                    <i class="fa-solid fa-pen text-[10px]"></i>
                                    <span>Edit</span>
                                </button>
                                <button type="button" 
                                    @click='openDelete({ id: {{ $p->id }}, name: @json($p->name) })'
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition" title="Remove profile">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>

                            <!-- Convert to Independent Account Button -->
                            <button type="button" 
                                @click='openPromote({ id: {{ $p->id }}, name: @json($p->name) })'
                                class="px-2.5 py-1.5 rounded-lg bg-sky-50 dark:bg-sky-500/15 hover:bg-sky-100 dark:hover:bg-sky-500/25 border border-sky-300 dark:border-sky-500/30 text-sky-700 dark:text-sky-300 font-bold text-xs transition flex items-center gap-1.5">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                <span>Make Independent Account</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- ============================================== -->
        <!-- MODAL 1: CREATE NEW CONNECTION                 -->
        <!-- ============================================== -->
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div @click.outside="showCreateModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-user-plus text-sky-500"></i>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">New Connected Profile</h3>
                    </div>
                    <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-white p-1">
                        <i class="fa-solid fa-times text-xs"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('connected_accounts.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Player Full Name</label>
                        <input type="text" name="name" required placeholder="e.g. Alex Smith" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 focus:outline-none transition">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Position</label>
                            <select name="role_preference" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 focus:outline-none cursor-pointer">
                                <option value="player">Skater (Player)</option>
                                <option value="goalie">Goalie</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Jersey #</label>
                            <input type="number" min="0" max="99" name="jersey_number" placeholder="e.g. 17" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Skill Rating (1 to 5)</label>
                        <select name="level" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 focus:outline-none cursor-pointer">
                            <option value="5">Level 5 (Elite / Competitive)</option>
                            <option value="4">Level 4 (Advanced)</option>
                            <option value="3" selected>Level 3 (Average / Mid-tier)</option>
                            <option value="2">Level 2 (Developing)</option>
                            <option value="1">Level 1 (Novice)</option>
                        </select>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">This rating is used by the team-sorting algorithm to balance teams evenly.</p>
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-white transition">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-600 to-indigo-600 dark:from-cyan-500 dark:to-sky-600 text-white dark:text-slate-950 font-bold text-xs transition shadow-md">Create Connected Profile</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODAL 2: TAKE CONTROL OF AN EXISTING GUEST     -->
        <!-- ============================================== -->
        <div x-show="showClaimModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div @click.outside="showClaimModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-link text-sky-500"></i>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Take Control of a Guest</h3>
                    </div>
                    <button type="button" @click="showClaimModal = false" class="text-slate-400 hover:text-white p-1">
                        <i class="fa-solid fa-times text-xs"></i>
                    </button>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    Convert a guest from past games into a permanent connected profile under your account. Their game history and attendance will be linked!
                </p>

                <form method="POST" action="{{ route('connected_accounts.claim_guest') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Guest Name</label>
                        <input list="past-guests-list" name="guest_name" required placeholder="Type or select past guest name..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 focus:outline-none transition">
                        <datalist id="past-guests-list">
                            @foreach($availableGuests as $g)
                                <option value="{{ $g->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Position</label>
                            <select name="role_preference" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 focus:outline-none cursor-pointer">
                                <option value="player">Skater (Player)</option>
                                <option value="goalie">Goalie</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Jersey #</label>
                            <input type="number" min="0" max="99" name="jersey_number" placeholder="e.g. 29" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Skill Rating (1 to 5)</label>
                        <select name="level" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 focus:outline-none cursor-pointer">
                            <option value="5">Level 5 (Elite)</option>
                            <option value="4">Level 4 (Advanced)</option>
                            <option value="3" selected>Level 3 (Average)</option>
                            <option value="2">Level 2 (Developing)</option>
                            <option value="1">Level 1 (Novice)</option>
                        </select>
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2">
                        <button type="button" @click="showClaimModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-white transition">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-600 to-indigo-600 dark:from-cyan-500 dark:to-sky-600 text-white dark:text-slate-950 font-bold text-xs transition shadow-md">Take Control of Guest</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODAL 3: PROMOTE TO INDEPENDENT (WITH WARNING) -->
        <!-- ============================================== -->
        <div x-show="showPromoteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-sm p-4">
            <div @click.outside="showPromoteModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 max-w-lg w-full shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-500 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                            Convert <span class="text-sky-600 dark:text-cyan-400" x-text="selectedProfile?.name"></span> to Independent Account
                        </h3>
                    </div>
                    <button type="button" @click="showPromoteModal = false" class="text-slate-400 hover:text-white p-1">
                        <i class="fa-solid fa-times text-xs"></i>
                    </button>
                </div>

                <!-- EXPLICIT UNLINKING WARNING -->
                <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-300 dark:border-amber-500/30 text-amber-900 dark:text-amber-200 text-xs space-y-2">
                    <div class="flex items-center gap-2 font-bold text-amber-800 dark:text-amber-300 text-sm">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                        <span>Warning: Account Will Be Unlinked</span>
                    </div>
                    <p class="leading-relaxed">
                        Once you create login credentials below, <strong class="text-amber-950 dark:text-white" x-text="selectedProfile?.name"></strong> will be <span class="underline font-bold">permanently unlinked</span> from your account and will become their own independent user.
                    </p>
                    <p class="leading-relaxed">
                        You will no longer manage their game sign-ups or profile settings from your account. The player will log in directly using their new email and password, while keeping all their past game history, stats, and jersey number.
                    </p>
                </div>

                <form method="POST" :action="selectedProfile ? `/connected-accounts/${selectedProfile.id}/promote` : '#'" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Player Email / Login Username</label>
                        <input type="email" name="email" required placeholder="player@example.com" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 focus:outline-none transition">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">This email will be used by the player to log in to Pickup Puck.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">New Password</label>
                            <input type="password" name="password" required minlength="8" placeholder="••••••••" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Confirm Password</label>
                            <input type="password" name="password_confirmation" required minlength="8" placeholder="••••••••" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2.5">
                        <button type="button" @click="showPromoteModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-white transition">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs transition shadow-md flex items-center gap-1.5">
                            <i class="fa-solid fa-unlink text-xs"></i>
                            <span>Unlink &amp; Create Independent Account</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODAL 4: EDIT CONNECTED PROFILE                -->
        <!-- ============================================== -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div @click.outside="showEditModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-pen text-sky-500"></i>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Edit Connected Profile</h3>
                    </div>
                    <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-white p-1">
                        <i class="fa-solid fa-times text-xs"></i>
                    </button>
                </div>

                <form method="POST" :action="selectedProfile ? `/connected-accounts/${selectedProfile.id}/update` : '#'" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Player Name</label>
                        <input type="text" name="name" required :value="selectedProfile?.name" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 focus:outline-none transition">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Position</label>
                            <select name="role_preference" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 focus:outline-none cursor-pointer">
                                <option value="player" :selected="selectedProfile?.role_preference === 'player'">Skater (Player)</option>
                                <option value="goalie" :selected="selectedProfile?.role_preference === 'goalie'">Goalie</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Jersey #</label>
                            <input type="number" min="0" max="99" name="jersey_number" :value="selectedProfile?.jersey_number" placeholder="e.g. 17" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 text-xs font-mono uppercase tracking-wider mb-1.5">Skill Rating (1 to 5)</label>
                        <select name="level" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-sky-500 focus:outline-none cursor-pointer">
                            <option value="5" :selected="selectedProfile?.level == 5">Level 5 (Elite)</option>
                            <option value="4" :selected="selectedProfile?.level == 4">Level 4 (Advanced)</option>
                            <option value="3" :selected="selectedProfile?.level == 3">Level 3 (Average)</option>
                            <option value="2" :selected="selectedProfile?.level == 2">Level 2 (Developing)</option>
                            <option value="1" :selected="selectedProfile?.level == 1">Level 1 (Novice)</option>
                        </select>
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-white transition">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-600 to-indigo-600 dark:from-cyan-500 dark:to-sky-600 text-white dark:text-slate-950 font-bold text-xs transition shadow-md">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODAL 5: DELETE / REMOVE CONFIRMATION          -->
        <!-- ============================================== -->
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div @click.outside="showDeleteModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 max-w-sm w-full shadow-2xl space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-500 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-trash-can text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Remove Connection</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Are you sure you want to remove <span class="font-bold text-white" x-text="selectedProfile?.name"></span> from your connected profiles?</p>
                    </div>
                </div>

                <form method="POST" :action="selectedProfile ? `/connected-accounts/${selectedProfile.id}` : '#'">
                    @csrf
                    @method('DELETE')
                    <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2">
                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-white transition">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition shadow-md">Remove</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
