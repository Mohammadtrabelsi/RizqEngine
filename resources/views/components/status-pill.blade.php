{{-- resources/views/components/status-pill.blade.php --}}
@props(['status'])

@php
    $map = [
        'paid'    => ['bg-ok-bg text-ok', __('status.paid')],
        'partial' => ['bg-warn-bg text-warn', __('status.partial')],
        'return'  => ['bg-danger-bg text-danger', __('status.return')],
        'draft'   => ['bg-canvas-2 text-ink-3', __('status.draft')],
    ];
    [$classes, $label] = $map[$status] ?? $map['draft'];
@endphp

<span class="rounded-full px-[9px] py-[3px] text-xs font-semibold {{ $classes }}">{{ $label }}</span>
