<!-- Pickup Puck PWA Install UI & Offline Indicator -->

<!-- Floating Offline Indicator Pill -->
<div id="pwa-offline-indicator" class="hidden fixed top-20 left-1/2 -translate-x-1/2 z-50 pointer-events-none">
    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-rose-950/90 border border-rose-500/50 text-rose-200 text-xs font-semibold shadow-xl backdrop-blur-md">
        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
        <i class="fa-solid fa-wifi-slash text-xs text-rose-400"></i>
        <span>Offline Mode — Cached Locker Room Active</span>
    </div>
</div>

<!-- Floating PWA Install Prompt Banner (Mobile & Desktop) -->
<div id="pwa-install-banner" class="hidden fixed bottom-20 lg:bottom-6 left-3 right-3 sm:left-auto sm:right-6 sm:max-w-md z-40 transform translate-y-full opacity-0 transition-all duration-300 pointer-events-auto">
    <div class="relative overflow-hidden p-4 rounded-2xl bg-slate-900/95 dark:bg-slate-950/95 backdrop-blur-xl border border-sky-500/30 shadow-[0_10px_35px_rgba(0,0,0,0.6)] text-white">
        <!-- Glow accent -->
        <div class="absolute -right-12 -top-12 w-32 h-32 bg-sky-500/20 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex items-center gap-3.5 relative z-10">
            <!-- App Icon -->
            <img src="{{ asset('icons/icon-96x96.png') }}" alt="Pickup Puck Icon" class="w-12 h-12 rounded-xl border border-sky-400/30 shadow-md bg-slate-950 object-cover shrink-0">

            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <h4 class="text-sm font-black text-white tracking-tight leading-tight">Install Pickup Puck</h4>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold uppercase bg-sky-500/20 text-sky-300 border border-sky-500/30">App</span>
                </div>
                <p class="text-[11px] text-slate-300 leading-snug mt-0.5 line-clamp-1">Install to your home screen for fast access &amp; game alerts.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-1.5 shrink-0">
                <button type="button" class="pwa-install-trigger px-3.5 py-2 rounded-xl bg-gradient-to-r from-sky-500 to-cyan-400 hover:from-sky-400 hover:to-cyan-300 text-slate-950 font-black text-xs transition shadow-md shadow-sky-500/20 flex items-center gap-1.5">
                    <i class="fa-solid fa-download text-xs"></i>
                    <span>Install</span>
                </button>
                <button type="button" id="pwa-dismiss-banner-btn" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition" title="Dismiss">
                    <i class="fa-solid fa-times text-xs"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- iOS Safari "Add to Home Screen" Bottom Sheet / Modal -->
<div id="pwa-ios-modal" class="hidden fixed inset-0 z-50 opacity-0 transition-opacity duration-300">
    <!-- Backdrop -->
    <div id="pwa-ios-modal-backdrop" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"></div>

    <!-- Bottom Sheet Content -->
    <div class="fixed inset-x-0 bottom-0 max-w-lg mx-auto p-4 pb-8 sm:p-6 sm:bottom-auto sm:top-1/2 sm:-translate-y-1/2">
        <div class="pwa-ios-drawer transform translate-y-full sm:translate-y-0 transition-transform duration-300 relative rounded-3xl bg-slate-900 border border-slate-700/80 shadow-2xl p-6 text-white overflow-hidden">
            <!-- Glow background -->
            <div class="absolute -right-20 -top-20 w-48 h-48 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('icons/apple-touch-icon-180x180.png') }}" alt="Pickup Puck" class="w-12 h-12 rounded-2xl shadow-lg border border-sky-500/30">
                    <div>
                        <h3 class="text-base font-black text-white">Install on iPhone / iPad</h3>
                        <p class="text-xs text-sky-400 font-medium">Add Pickup Puck to your Home Screen</p>
                    </div>
                </div>
                <button type="button" id="pwa-ios-modal-close" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition">
                    <i class="fa-solid fa-times text-xs"></i>
                </button>
            </div>

            <!-- Step by step instructions -->
            <div class="py-5 space-y-4">
                <!-- Step 1 -->
                <div class="flex items-start gap-3.5">
                    <div class="w-7 h-7 rounded-full bg-sky-500/20 text-sky-400 border border-sky-500/30 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                        1
                    </div>
                    <div class="text-xs text-slate-200 leading-relaxed">
                        In Safari, tap the <span class="font-bold text-white">Share</span> button at the bottom of the screen:
                        <div class="mt-1.5 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800 border border-slate-700 text-sky-400 font-semibold">
                            <i class="fa-solid fa-arrow-up-from-bracket"></i>
                            <span>Share Icon</span>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex items-start gap-3.5">
                    <div class="w-7 h-7 rounded-full bg-sky-500/20 text-sky-400 border border-sky-500/30 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                        2
                    </div>
                    <div class="text-xs text-slate-200 leading-relaxed">
                        Scroll down the menu and tap <span class="font-bold text-white">"Add to Home Screen"</span>:
                        <div class="mt-1.5 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800 border border-slate-700 text-emerald-400 font-semibold">
                            <i class="fa-regular fa-square-plus"></i>
                            <span>Add to Home Screen</span>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="flex items-start gap-3.5">
                    <div class="w-7 h-7 rounded-full bg-sky-500/20 text-sky-400 border border-sky-500/30 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                        3
                    </div>
                    <div class="text-xs text-slate-200 leading-relaxed">
                        Tap <span class="font-bold text-white">"Add"</span> in the top right corner to complete installation.
                    </div>
                </div>
            </div>

            <!-- Footer confirmation button -->
            <div class="pt-3 border-t border-slate-800">
                <button type="button" onclick="window.openPWAInstallGuide ? document.getElementById('pwa-ios-modal-close').click() : null" class="w-full py-3 rounded-xl bg-gradient-to-r from-sky-500 to-cyan-400 hover:from-sky-400 hover:to-cyan-300 text-slate-950 font-black text-xs transition shadow-lg shadow-sky-500/20 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Got It!</span>
                </button>
            </div>
        </div>
    </div>
</div>
