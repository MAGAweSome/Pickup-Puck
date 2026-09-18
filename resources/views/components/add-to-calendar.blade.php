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

<div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    <button
        type="button"
        @click="open = !open"
        class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-md bg-slate-700/90 hover:bg-slate-600 text-ice border border-slate-600/70 hover:border-slate-500 shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-ice-blue/40"
        title="Add this game to your calendar"
        aria-haspopup="true"
        :aria-expanded="open"
    >
        <svg class="w-3.5 h-3.5 text-ice-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span>Add to Calendar</span>
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
        class="absolute right-0 sm:right-auto sm:left-0 mt-2 w-56 rounded-lg bg-slate-900 border border-slate-700 shadow-2xl z-50 py-1 text-xs divide-y divide-slate-800"
    >
        <div class="px-3 py-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase bg-slate-950/40">
            Select Calendar
        </div>

        <div class="py-1">
            <!-- Google Calendar -->
            <a
                href="{{ $googleCalUrl }}"
                target="_blank"
                rel="noopener noreferrer"
                @click="open = false"
                class="flex items-center gap-2.5 px-3 py-2 text-slate-200 hover:text-white hover:bg-slate-800 transition"
            >
                <i class="fa-brands fa-google text-rose-400 text-sm w-4 text-center"></i>
                <div>
                    <div class="font-medium">Google Calendar</div>
                    <div class="text-[10px] text-slate-400">Opens web in new tab</div>
                </div>
            </a>

            <!-- Apple Calendar (.ics) -->
            <a
                href="{{ $icsUrl }}"
                download
                @click="open = false"
                class="flex items-center gap-2.5 px-3 py-2 text-slate-200 hover:text-white hover:bg-slate-800 transition"
            >
                <i class="fa-brands fa-apple text-slate-200 text-sm w-4 text-center"></i>
                <div>
                    <div class="font-medium">Apple Calendar</div>
                    <div class="text-[10px] text-slate-400">iOS / macOS (.ics + alarms)</div>
                </div>
            </a>

            <!-- Outlook / Other (.ics) -->
            <a
                href="{{ $icsUrl }}"
                download
                @click="open = false"
                class="flex items-center gap-2.5 px-3 py-2 text-slate-200 hover:text-white hover:bg-slate-800 transition"
            >
                <i class="fa-solid fa-calendar-day text-blue-400 text-sm w-4 text-center"></i>
                <div>
                    <div class="font-medium">Outlook / iCal File</div>
                    <div class="text-[10px] text-slate-400">Universal .ics file</div>
                </div>
            </a>
        </div>

        <div class="px-3 py-1.5 text-[10px] text-slate-400 bg-slate-950/20">
            🔔 Includes alerts at 2h & 30m prior
        </div>
    </div>
</div>
