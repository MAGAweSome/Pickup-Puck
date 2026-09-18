@props(['game' => null])

@php
    $targetGame = $game ?? ($attributes['game'] ?? null);
    if (!$targetGame) return;

    $startUtc = $targetGame->time->copy()->setTimezone('UTC')->format('Ymd\THis\Z');
    $duration = (int) ($targetGame->duration ?: 60);
    $endUtc = $targetGame->time->copy()->addMinutes($duration)->setTimezone('UTC')->format('Ymd\THis\Z');
    $gameUrl = route('game_detail.game_id', $targetGame->id);
    $desc = "Pickup Hockey League\nLocation: {$targetGame->location}\nGame Page: {$gameUrl}\n\nNote: Teams reveal 30 minutes before puck drop!";
    $googleCalUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE'
        . '&text=' . urlencode('Pickup Puck: ' . ($targetGame->title ?: 'Hockey Game'))
        . '&dates=' . $startUtc . '/' . $endUtc
        . '&details=' . urlencode($desc)
        . '&location=' . urlencode($targetGame->location ?: 'Hockey Arena');
    $icsUrl = route('game.calendar.ics', $targetGame->id);
@endphp

<div class="relative inline-block text-left" 
    :class="{ 'z-50': open }"
    x-data="{ 
        open: false, 
        dropUp: false,
        toggle() {
            if (!this.open) {
                const rect = this.$el.getBoundingClientRect();
                const spaceBelow = window.innerHeight - rect.bottom;
                this.dropUp = spaceBelow < 280 && rect.top > 250;
            }
            this.open = !this.open;
            if (this.open) {
                this.$el.closest('.relative')?.classList.add('z-40');
            } else {
                this.$el.closest('.relative')?.classList.remove('z-40');
            }
        }
    }" 
    @click.outside="open = false; $el.closest('.relative')?.classList.remove('z-40')" 
    @keydown.escape.window="open = false; $el.closest('.relative')?.classList.remove('z-40')">
    <button
        type="button"
        @click="toggle()"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-white dark:bg-slate-800/80 hover:bg-slate-50 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white border border-slate-300 dark:border-slate-700/80 hover:border-sky-400 dark:hover:border-sky-500/40 shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-sky-500/40"
        aria-label="Add this game to your calendar"
        aria-haspopup="true"
        :aria-expanded="open"
    >
        <svg class="w-3.5 h-3.5 text-sky-500 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span>Calendar</span>
        <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        :class="dropUp ? 'bottom-full mb-2 right-0 origin-bottom-right' : 'top-full mt-2 right-0 origin-top-right'"
        class="absolute w-56 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-xl dark:shadow-2xl z-[100] py-1 text-xs divide-y divide-slate-100 dark:divide-slate-800"
    >
        <div class="px-3 py-2 text-[11px] font-bold tracking-wider text-slate-500 dark:text-slate-400 uppercase bg-slate-50 dark:bg-slate-950/40">
            Select Calendar
        </div>

        <div class="py-1">
            <!-- Google Calendar -->
            <a
                href="{{ $googleCalUrl }}"
                target="_blank"
                rel="noopener noreferrer"
                @click="open = false"
                class="flex items-center gap-2.5 px-3 py-2 text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 transition no-underline"
            >
                <i class="fa-brands fa-google text-rose-500 text-sm w-4 text-center"></i>
                <div>
                    <div class="font-bold">Google Calendar</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400">Opens web in new tab</div>
                </div>
            </a>

            <!-- Apple Calendar (.ics) -->
            <a
                href="{{ $icsUrl }}"
                download
                @click="open = false"
                class="flex items-center gap-2.5 px-3 py-2 text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 transition no-underline"
            >
                <i class="fa-brands fa-apple text-slate-800 dark:text-slate-200 text-sm w-4 text-center"></i>
                <div>
                    <div class="font-bold">Apple Calendar</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400">iOS / macOS (.ics + alarms)</div>
                </div>
            </a>

            <!-- Outlook / Other (.ics) -->
            <a
                href="{{ $icsUrl }}"
                download
                @click="open = false"
                class="flex items-center gap-2.5 px-3 py-2 text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 transition no-underline"
            >
                <i class="fa-solid fa-calendar-day text-sky-600 dark:text-sky-400 text-sm w-4 text-center"></i>
                <div>
                    <div class="font-bold">Outlook / iCal File</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400">Universal .ics file</div>
                </div>
            </a>
        </div>

        <div class="px-3 py-1.5 text-[10px] text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-950/20">
            🔔 Auto-configures alerts 2h &amp; 30m prior
        </div>
    </div>
</div>
