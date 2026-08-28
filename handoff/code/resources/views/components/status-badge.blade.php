@props([
    'status' => 'paid',   // paid | partial | return | pending
])

@php
    // Maps a transaction status to the pill palette from the design tokens.
    $map = [
        'paid'    => 'bg-success-bg text-success-fg',
        'partial' => 'bg-warn-bg text-warn',
        'return'  => 'bg-danger-bg text-danger-fg',
        'pending' => 'bg-[oklch(0.96_0.005_280)] text-muted',
    ];
    $classes = $map[$status] ?? $map['pending'];
    $label = __('pos.status.'.$status);
@endphp

<span {{ $attributes->merge(['class' => "inline-block rounded-full px-[9px] py-[3px] text-xs font-semibold $classes"]) }}>
    {{ $label }}
</span>
