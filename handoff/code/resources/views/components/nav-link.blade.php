@props([
    'href' => '#',
    'active' => false,
    'badge' => null,   // optional count pill (e.g. open orders)
    'dot' => false,    // leading status dot (used by top-level items)
])

<a href="{{ $href }}"
   @if ($active) aria-current="page" @endif
   {{ $attributes->class([
        'flex items-center gap-[11px] rounded-[10px] px-3 py-2.5 text-sm transition',
        'bg-brand font-semibold text-white' => $active,
        'font-medium text-[oklch(0.82_0.01_280)] hover:bg-ink-2 hover:text-white' => ! $active,
        'justify-between' => $badge !== null,
   ]) }}>
    <span class="flex items-center gap-[11px]">
        @if ($dot)
            <span class="h-1.5 w-1.5 rounded-full {{ $active ? 'bg-white' : 'bg-[oklch(0.45_0.02_277)]' }}"></span>
        @endif
        {{ $slot }}
    </span>
    @if ($badge !== null)
        <span class="rounded-full bg-ink-3 px-[7px] py-0.5 font-mono text-[11px] font-semibold">{{ $badge }}</span>
    @endif
</a>
