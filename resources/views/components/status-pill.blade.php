{{-- resources/views/components/status-pill.blade.php --}}
@props(['status' => 'draft'])

@php
    [$classes, $label] = match ($status) {
        'paid' => ['bg-ok-bg text-ok', __('status.paid')],
        'partial' => ['bg-warn-bg text-warn', __('status.partial')],
        'return' => ['bg-danger-bg text-danger', __('status.return')],
        default => ['bg-canvas-2 text-ink-3', __('status.draft')],
    };
@endphp

<span class="rounded-full px-[9px] py-[3px] text-xs font-semibold {{ $classes }}">{{ $label }}</span>
