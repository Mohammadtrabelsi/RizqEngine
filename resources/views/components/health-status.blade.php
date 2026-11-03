{{-- Status badge for a health / backup check (spatie/laravel-health statuses). --}}
@props(['status' => 'skipped'])

@php
    [$classes, $label] = match ($status) {
        'ok' => ['bg-ok-bg text-ok', __('system.status_ok')],
        'warning' => ['bg-warn-bg text-warn', __('system.status_warning')],
        'failed', 'crashed' => ['bg-danger-bg text-danger', __('system.status_failed')],
        default => ['bg-canvas-2 text-ink-3', __('system.status_skipped')],
    };
@endphp

<span class="rounded-full px-[9px] py-[3px] text-xs font-semibold {{ $classes }}">{{ $label }}</span>
