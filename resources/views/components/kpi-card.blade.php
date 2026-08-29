{{-- resources/views/components/kpi-card.blade.php --}}
@props(['label', 'value', 'chip' => null, 'chipTone' => 'neutral', 'footer' => null, 'tone' => 'default'])

@php
    $card = $tone === 'warn'
        ? 'border-warn-border bg-warn-bg'
        : 'border-hairline bg-white';

    $chipClasses = match ($chipTone) {
        'ok'   => 'bg-ok-bg text-ok',
        'warn' => 'bg-warn-bg text-warn',
        default => 'bg-canvas-2 text-ink-3',
    };
@endphp

<div class="rounded-card border p-5 {{ $card }}">
    <div class="flex items-start justify-between">
        <span @class(['font-mono text-[10.5px] font-semibold tracking-micro', 'text-warn' => $tone === 'warn', 'text-body' => $tone !== 'warn'])>
            {{ $label }}
        </span>
        @if ($chip)
            <span class="rounded-md px-[7px] py-[3px] font-mono text-[11px] font-semibold {{ $chipClasses }}">{{ $chip }}</span>
        @endif
    </div>

    <p @class(['mt-3.5 font-mono text-[32px] font-semibold tracking-num', 'text-warn' => $tone === 'warn'])>{{ $value }}</p>

    @if ($footer)
        <p @class(['mt-2.5 text-[13px]', 'text-warn' => $tone === 'warn', 'text-body' => $tone !== 'warn'])>{{ $footer }}</p>
    @endif
</div>
