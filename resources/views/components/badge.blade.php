@php
    $status = $status ?? '';
    $classes = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
    if(strtolower($status) === 'paid') {
        $classes .= ' bg-green-600 text-white';
    } elseif(strtolower($status) === 'overdue' || strtolower($status) === 'not paid') {
        $classes .= ' bg-red-600 text-white';
    } else {
        $classes .= ' bg-slate-700 text-slate-200';
    }
@endphp

<span class="{{ $classes }}">{{ $status }}</span>
