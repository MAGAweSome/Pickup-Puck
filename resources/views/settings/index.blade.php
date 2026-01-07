@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold">Settings</h1>
        <p class="mt-2 text-slate-300">Application defaults and preferences.</p>

        @if(session('success'))
            <div class="mt-4 p-3 rounded bg-emerald-600 text-white">{{ session('success') }}</div>
        @endif

        <form action="{{ route('settings.update') }}" method="POST" class="mt-6 bg-slate-800 border border-slate-700 rounded-lg p-4">
            @csrf
            <h3 class="text-lg text-ice mb-3">Game Creation Defaults</h3>

            <div class="mb-4">
                <label class="block text-slate-300 text-sm">Title Template</label>
                <input type="text" name="default_title_template" value="{{ old('default_title_template', $defaults['title_template'] ?? '') }}" placeholder="e.g. Game {n} or Pickup {n}" class="mt-1 w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">
                <p class="text-slate-400 text-xs mt-1">Use <code>{n}</code> where the incrementing number should appear. The system will pick the next number based on existing game titles.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 text-sm">Default Time</label>
                    <div class="relative input-with-icon mt-1">
                        <input type="time" id="default_time" name="default_time" value="{{ old('default_time', isset($defaults['time']) ? \Carbon\Carbon::parse($defaults['time'])->format('H:i') : '') }}" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">
                        <span class="input-icon text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="12" cy="12" r="9"></circle>
                                <polyline points="12 7 12 12 15 15"></polyline>
                            </svg>
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-300 text-sm">Default Duration (min)</label>
                    <input type="number" name="default_duration" min="1" value="{{ old('default_duration', $defaults['duration'] ?? '') }}" class="mt-1 w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-slate-300 text-sm">Default Location</label>
                    <input type="text" name="default_location" value="{{ old('default_location', $defaults['location'] ?? '') }}" class="mt-1 w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">
                </div>

                <div>
                    <label class="block text-slate-300 text-sm">Default Price</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-md bg-slate-900 border border-r-0 border-slate-700 text-slate-300">$</span>
                        <input type="number" step="0.01" min="0" name="default_price" value="{{ old('default_price', $defaults['price'] ?? '') }}" class="w-full bg-slate-900 text-gray-100 border border-slate-700 rounded-r-md px-3 py-2">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-300 text-sm">Default Season</label>
                    <select name="default_season_id" class="mt-1 w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-ice">
                        <option value="">(Use no default)</option>
                        @foreach($seasons as $season)
                            <option value="{{ $season->id }}" {{ old('default_season_id', $defaults['season_id'] ?? '') == $season->id ? 'selected' : '' }}>Season {{ $season->season_number }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            
            <div class="mt-4">
                <button type="submit" class="px-4 py-2 bg-ice-blue text-deep-navy rounded">Save</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Hide native picker indicators via CSS (for WebKit)
    const style = document.createElement('style');
    style.innerHTML = `
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="time"]::-webkit-calendar-picker-indicator {
            -webkit-appearance: none;
            appearance: none;
            display: none;
        }
        .input-with-icon input { padding-right: 2.75rem; }
        .input-with-icon .input-icon { position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); pointer-events: none; }
    `;
    document.head.appendChild(style);

    const inputs = document.querySelectorAll('input[type="date"], input[type="time"]');
    inputs.forEach(function (el) {
        // Make cursor indicate clickable
        el.style.cursor = 'pointer';

        const openPicker = function () {
            if (typeof el.showPicker === 'function') {
                try { el.showPicker(); } catch (e) { el.focus(); }
            } else {
                el.focus();
            }
        };

        el.addEventListener('click', openPicker);
        el.addEventListener('focus', openPicker);
    });
});
</script>
@endpush
