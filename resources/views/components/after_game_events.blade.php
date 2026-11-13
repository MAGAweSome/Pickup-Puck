{{-- After Game Hangouts & Events Hub --}}
<div id="afterGameSection" class="rounded-3xl bg-white dark:bg-gradient-to-b dark:from-slate-900/90 dark:to-slate-950/90 border border-slate-200 dark:border-slate-800 p-6 shadow-xl backdrop-blur-md" x-data="{ showCreateModal: false, selectedVenue: 'Beertown' }">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 mb-5 border-b border-slate-200 dark:border-slate-800 gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-amber-500/15 dark:bg-amber-400/10 border border-amber-500/30 dark:border-amber-400/30 flex items-center justify-center text-amber-600 dark:text-amber-400 shadow-sm shrink-0">
                <i class="fa-solid fa-beer-mug-empty text-lg"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-wide flex items-center gap-2">
                    <span>After Game Hangouts</span>
                    <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-400/10 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-400/20">
                        {{ $afterGameEvents->count() }} {{ Str::plural('Event', $afterGameEvents->count()) }}
                    </span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Where are we heading after the ice? Create a hangout or join a table!</p>
            </div>
        </div>

        <div>
            <button
                type="button"
                @click="showCreateModal = true"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-slate-950 font-black rounded-xl text-xs uppercase tracking-wider transition shadow-md shadow-amber-500/20 transform active:scale-95"
            >
                <i class="fa-solid fa-plus font-black"></i>
                <span>Add After Game Event</span>
            </button>
        </div>
    </div>

    @if($afterGameEvents->isEmpty())
        <div class="py-10 text-center rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-950/40 p-6">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-amber-500/10 dark:bg-amber-400/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl shadow-inner">
                <i class="fa-solid fa-utensils"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">No after game plans posted yet</h4>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-1 mb-4">
                Be the first to call the spot! Create an event for Beertown or your favorite post-game joint and see who else is coming.
            </p>
            <button
                type="button"
                @click="showCreateModal = true"
                class="inline-flex items-center gap-2 px-4 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold transition"
            >
                <i class="fa-solid fa-location-dot text-amber-500"></i>
                <span>Propose Beertown or another spot</span>
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($afterGameEvents as $event)
                @php
                    $isCreator = auth()->check() && (auth()->id() === $event->creator_id || auth()->user()->hasRole('admin'));
                    $isAttending = auth()->check() && $event->isUserAttending(auth()->id());
                    $attendees = $event->attendees;
                    $mapSearchUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($event->location);
                @endphp
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-950/70 p-5 flex flex-col justify-between shadow-md hover:border-amber-500/40 transition">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-black uppercase font-mono tracking-wider text-amber-700 dark:text-amber-400 px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-400/10 border border-amber-300 dark:border-amber-400/20 inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-location-dot text-[10px]"></i>
                                        <span>{{ $event->title ?: 'Post-Game Spot' }}</span>
                                    </span>
                                </div>
                                <h4 class="text-base font-black text-slate-900 dark:text-white truncate" title="{{ $event->location }}">
                                    {{ $event->location }}
                                </h4>
                            </div>

                            @if($isCreator)
                                <form method="POST" action="{{ route('after_game_events.destroy', [$game->id, $event->id]) }}" onsubmit="return confirm('Remove this after-game event?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-rose-500 dark:hover:text-rose-400 text-xs p-1 transition" title="Delete event">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            @endif
                        </div>

                        @if($event->description)
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 mb-3 line-clamp-2 italic">
                                “{{ $event->description }}”
                            </p>
                        @endif

                        <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 pt-1 pb-3 border-b border-slate-200 dark:border-slate-800/80 font-mono">
                            <span>Posted by <strong class="text-slate-700 dark:text-slate-300">{{ $event->creator->name ?? 'A Player' }}</strong></span>
                            <a href="{{ $mapSearchUrl }}" target="_blank" rel="noopener noreferrer" class="text-sky-600 dark:text-cyan-400 hover:underline inline-flex items-center gap-1">
                                <span>Directions</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                            </a>
                        </div>

                        {{-- Attendees list --}}
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-users text-amber-500"></i>
                                    <span>Who's Going</span>
                                </span>
                                <span class="font-mono text-[11px] px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $attendees->count() }} {{ Str::plural('player', $attendees->count()) }}
                                </span>
                            </div>

                            <div class="flex flex-wrap gap-1.5 max-h-36 overflow-y-auto pr-1">
                                @forelse($attendees as $attendee)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 shadow-2xs">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-[10px] font-mono font-bold text-slate-600 dark:text-slate-400">
                                            {{ strtoupper(substr($attendee->name, 0, 1)) }}
                                        </span>
                                        <span class="font-medium truncate max-w-[120px]">{{ $attendee->name }}</span>
                                        @if($attendee->id === $event->creator_id)
                                            <span class="text-[9px] font-mono text-amber-600 dark:text-amber-400" title="Organizer">★</span>
                                        @endif
                                        @if(auth()->check() && auth()->id() === $attendee->id)
                                            <span class="text-[9px] font-mono text-sky-600 dark:text-cyan-400 font-bold">(You)</span>
                                        @endif
                                    </span>
                                @empty
                                    <span class="text-xs text-slate-400 italic">No attendees signed up yet.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Actions: Join / Leave / Managed Profiles signup --}}
                    <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-800/80 space-y-2">
                        @if($isAttending)
                            <form method="POST" action="{{ route('after_game_events.leave', [$game->id, $event->id]) }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full py-2 px-3 rounded-xl border border-rose-300 dark:border-rose-500/30 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-500/20 text-xs font-bold transition flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-user-minus"></i>
                                    <span>You're Going (Click to Leave)</span>
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('after_game_events.join', [$game->id, $event->id]) }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold transition shadow-sm flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-user-plus"></i>
                                    <span>Count Me In!</span>
                                </button>
                            </form>
                        @endif

                        {{-- If manager has connected profiles, allow signing them up too --}}
                        @if(isset($managedProfiles) && $managedProfiles->isNotEmpty())
                            <div class="pt-1" x-data="{ openProfiles: false }">
                                <button
                                    type="button"
                                    @click="openProfiles = !openProfiles"
                                    class="text-[11px] font-mono text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 inline-flex items-center gap-1"
                                >
                                    <i class="fa-solid fa-users-gear text-[10px]"></i>
                                    <span>Manage connected profiles RSVP</span>
                                    <i class="fa-solid fa-chevron-down text-[8px] transition" :class="{'rotate-180': openProfiles}"></i>
                                </button>

                                <div x-show="openProfiles" x-cloak class="mt-2 p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-1.5">
                                    @foreach($managedProfiles as $profile)
                                        @php $profileAttending = $event->isUserAttending($profile->id); @endphp
                                        <div class="flex items-center justify-between text-xs py-1 px-1.5 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                            <span class="font-medium text-slate-800 dark:text-slate-200 truncate">{{ $profile->name }}</span>
                                            @if($profileAttending)
                                                <form method="POST" action="{{ route('after_game_events.leave', [$game->id, $event->id]) }}" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $profile->id }}">
                                                    <button type="submit" class="text-[10px] font-mono text-rose-500 hover:underline">Remove</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('after_game_events.join', [$game->id, $event->id]) }}" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $profile->id }}">
                                                    <button type="submit" class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold hover:underline">+ Sign Up</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Create Event Modal --}}
    <div
        x-show="showCreateModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
        @keydown.escape.window="showCreateModal = false"
    >
        <div
            @click.away="showCreateModal = false"
            class="w-full max-w-lg rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-2xl relative"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <i class="fa-solid fa-beer-mug-empty"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-slate-900 dark:text-white">New After Game Event</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">You will be automatically signed up upon creation.</p>
                    </div>
                </div>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('after_game_events.store', $game->id) }}" class="space-y-4">
                @csrf

                {{-- Quick Venue Suggestions --}}
                <div>
                    <label class="block text-xs font-bold uppercase font-mono tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                        Quick Picks
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            @click="selectedVenue = 'Beertown Public House'; document.getElementById('eventLocationInput').value = 'Beertown Public House'; document.getElementById('eventTitleInput').value = 'Post-Game at Beertown';"
                            class="px-3 py-1.5 rounded-xl border border-amber-300 dark:border-amber-400/30 bg-amber-50 dark:bg-amber-400/10 hover:bg-amber-100 dark:hover:bg-amber-400/20 text-amber-800 dark:text-amber-300 text-xs font-bold flex items-center gap-1.5 transition"
                        >
                            <i class="fa-solid fa-beer-mug-empty text-[11px]"></i>
                            <span>🍺 Beertown (Most Popular)</span>
                        </button>
                        <button
                            type="button"
                            @click="selectedVenue = 'Boston Pizza'; document.getElementById('eventLocationInput').value = 'Boston Pizza'; document.getElementById('eventTitleInput').value = 'Pizza & Wings';"
                            class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold flex items-center gap-1.5 transition"
                        >
                            <i class="fa-solid fa-pizza-slice text-[11px]"></i>
                            <span>Boston Pizza</span>
                        </button>
                        <button
                            type="button"
                            @click="selectedVenue = 'The Canadian Brewhouse'; document.getElementById('eventLocationInput').value = 'The Canadian Brewhouse'; document.getElementById('eventTitleInput').value = 'Brewhouse Drinks';"
                            class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold flex items-center gap-1.5 transition"
                        >
                            <i class="fa-solid fa-mug-hot text-[11px]"></i>
                            <span>The Canadian Brewhouse</span>
                        </button>
                    </div>
                </div>

                {{-- Location Input --}}
                <div>
                    <label for="eventLocationInput" class="block text-xs font-bold uppercase font-mono tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                        Event Location / Venue <span class="text-rose-500">*</span>
                    </label>
                    <input
                        id="eventLocationInput"
                        type="text"
                        name="location"
                        required
                        value="Beertown Public House"
                        placeholder="e.g. Beertown Public House (King St), Wild Wing, etc."
                        class="w-full rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500"
                    >
                </div>

                {{-- Event Title (Optional) --}}
                <div>
                    <label for="eventTitleInput" class="block text-xs font-bold uppercase font-mono tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                        Title / Event Name <span class="text-slate-400 font-normal lowercase">(optional)</span>
                    </label>
                    <input
                        id="eventTitleInput"
                        type="text"
                        name="title"
                        value="Post-Game at Beertown"
                        placeholder="e.g. Beers & Nachos, Quick Drink"
                        class="w-full rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500"
                    >
                </div>

                {{-- Description / Notes (Optional) --}}
                <div>
                    <label for="eventDescInput" class="block text-xs font-bold uppercase font-mono tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                        Notes & Details <span class="text-slate-400 font-normal lowercase">(optional)</span>
                    </label>
                    <textarea
                        id="eventDescInput"
                        name="description"
                        rows="2"
                        placeholder="e.g. Table under John, heading right after game at 10:45pm!"
                        class="w-full rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 px-3.5 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500"
                    ></textarea>
                </div>

                <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-400/10 border border-amber-200 dark:border-amber-400/20 text-xs text-amber-900 dark:text-amber-200 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-amber-600 dark:text-amber-400"></i>
                    <span>You (<strong>{{ auth()->user()->name }}</strong>) will be automatically signed up as attendee #1!</span>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button
                        type="button"
                        @click="showCreateModal = false"
                        class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-slate-950 font-black text-xs uppercase tracking-wider transition shadow-md"
                    >
                        Create & Join Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
