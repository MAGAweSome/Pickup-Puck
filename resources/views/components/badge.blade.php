@php
    $status = $status ?? '';
    $classes = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
    if(strtolower($status) === 'paid') {
        $classes .= ' bg-green-600 text-white';
    } elseif(strtolower($status) === 'attending') {
        // Show attending as a green pill with green text (preserve badge shape)
        $classes .= ' bg-slate-700 border border-green-400 text-green-400';
    } elseif(strtolower($status) === 'overdue' || strtolower($status) === 'not paid') {
        $classes .= ' bg-red-600 text-white';
    } else {
        $classes .= ' bg-slate-700 text-slate-200';
    }
@endphp

<span class="{{ $classes }}">{{ $status }}</span>
