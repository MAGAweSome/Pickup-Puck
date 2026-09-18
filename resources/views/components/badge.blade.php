@php
    $status = $status ?? '';
    $lower = strtolower($status);
    $base = 'inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full text-xs font-mono font-bold tracking-wide shadow-sm';
    
    if($lower === 'paid' || $lower === 'attending') {
        $classes = $base . ' bg-emerald-100 dark:bg-emerald-500/15 border border-emerald-300 dark:border-emerald-400/40 text-emerald-800 dark:text-emerald-300';
        $dot = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>';
    } elseif($lower === 'not attending' || $lower === 'not_attending') {
        $classes = $base . ' bg-rose-100 dark:bg-rose-500/15 border border-rose-300 dark:border-rose-400/40 text-rose-800 dark:text-rose-300';
        $dot = '<span class="w-1.5 h-1.5 rounded-full bg-rose-500 dark:bg-rose-400"></span>';
    } elseif($lower === 'overdue' || $lower === 'not paid') {
        $classes = $base . ' bg-amber-100 dark:bg-amber-500/15 border border-amber-300 dark:border-amber-400/40 text-amber-900 dark:text-amber-300';
        $dot = '<span class="w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-amber-400"></span>';
    } else {
        $classes = $base . ' bg-slate-100 dark:bg-slate-800/90 border border-slate-300 dark:border-slate-700/80 text-slate-700 dark:text-slate-300';
        $dot = '<span class="w-1.5 h-1.5 rounded-full bg-slate-500 dark:bg-slate-400"></span>';
    }
@endphp

<span class="{{ $classes }}">{!! $dot !!}<span>{{ $status }}</span></span>

