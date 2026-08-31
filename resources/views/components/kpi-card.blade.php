{{-- resources/views/components/kpi-card.blade.php --}}
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
