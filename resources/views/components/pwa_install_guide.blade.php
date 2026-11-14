<!-- How to Install the App Guide Card Component -->
<div id="app-install-guide" class="rounded-3xl bg-white dark:bg-gradient-to-b dark:from-slate-900/90 dark:to-slate-950/90 border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-xl backdrop-blur-md space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 dark:border-slate-800 gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-sky-500/15 border border-sky-500/30 text-sky-600 dark:text-cyan-400 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-mobile-screen-button"></i>
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>Install Pickup Puck App</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-sky-500/15 text-sky-700 dark:text-sky-300 border border-sky-500/30">Free</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Install on your phone or computer for instant game day alerts and faster loading.</p>
            </div>
        </div>
        <!-- <button type="button" class="pwa-install-trigger px-4 py-2 rounded-xl bg-gradient-to-r from-sky-500 to-cyan-400 hover:from-sky-400 hover:to-cyan-300 text-slate-950 font-black text-xs uppercase tracking-wider transition shadow-md shadow-sky-500/20 flex items-center gap-2 shrink-0 self-start sm:self-auto">
            <i class="fa-solid fa-download text-xs"></i>
            <span>Install Now</span>
        </button> -->
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
        <!-- Android -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800/80 space-y-2.5">
            <div class="flex items-center gap-2 font-bold text-slate-900 dark:text-white">
                <i class="fa-brands fa-android text-emerald-500 text-lg"></i>
                <span class="text-sm">Android (Chrome)</span>
            </div>
            <ol class="space-y-1.5 text-slate-600 dark:text-slate-300 list-decimal list-inside leading-relaxed text-[11px]">
                <li>Open <strong class="text-slate-800 dark:text-slate-100">Chrome</strong> on your phone.</li>
                <li>Tap the menu (<strong class="text-slate-800 dark:text-slate-100">⋮</strong>) in the top-right corner.</li>
                <li>Tap <strong class="text-slate-800 dark:text-slate-100">"Install app"</strong> (or "Add to Home screen").</li>
            </ol>
        </div>

        <!-- iPhone / iOS -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800/80 space-y-2.5">
            <div class="flex items-center gap-2 font-bold text-slate-900 dark:text-white">
                <i class="fa-brands fa-apple text-slate-700 dark:text-slate-200 text-lg"></i>
                <span class="text-sm">iPhone &amp; iPad (Safari)</span>
            </div>
            <ol class="space-y-1.5 text-slate-600 dark:text-slate-300 list-decimal list-inside leading-relaxed text-[11px]">
                <li>Open <strong class="text-slate-800 dark:text-slate-100">Safari</strong> on your device.</li>
                <li>Tap the <strong class="text-slate-800 dark:text-slate-100">Share</strong> button (square with arrow ↑).</li>
                <li>Scroll down and tap <strong class="text-slate-800 dark:text-slate-100">"Add to Home Screen"</strong>.</li>
            </ol>
        </div>

        <!-- Windows / Mac Desktop -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800/80 space-y-2.5">
            <div class="flex items-center gap-2 font-bold text-slate-900 dark:text-white">
                <i class="fa-solid fa-laptop text-sky-500 text-lg"></i>
                <span class="text-sm">Windows &amp; Mac (Desktop)</span>
            </div>
            <ol class="space-y-1.5 text-slate-600 dark:text-slate-300 list-decimal list-inside leading-relaxed text-[11px]">
                <li>Look in the address bar for the <strong class="text-slate-800 dark:text-slate-100">Install icon</strong> (⊕).</li>
                <li>Click <strong class="text-slate-800 dark:text-slate-100">"Install"</strong> to add the desktop app.</li>
                <li>Launches in its own sleek native window.</li>
            </ol>
        </div>
    </div>
</div>
