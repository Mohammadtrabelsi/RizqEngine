@props([
    'size' => 30,          // tile edge in px
    'tone' => 'light',     // 'light' on paper, 'dark' on the ink sidebar
    'showText' => true,
])

@php
    // Triangle mark: a CSS triangle centered in a rounded tile.
    $tile = $tone === 'dark' ? 'bg-ink-3' : 'bg-ink';
    $tri  = $tone === 'dark' ? 'border-b-brand-300' : 'border-b-brand-400';
    $b    = round($size * 0.23);   // triangle base half-width
    $h    = round($size * 0.40);   // triangle height
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-[11px]']) }}>
    <div class="grid place-items-center rounded-[9px] {{ $tile }}"
         style="width: {{ $size }}px; height: {{ $size }}px;">
        <span style="width:0;height:0;border-left:{{ $b }}px solid transparent;border-right:{{ $b }}px solid transparent;"
              class="border-b-[{{ $h }}px] {{ $tri }}"></span>
    </div>
    @if ($showText)
        <span class="font-display font-bold tracking-[-0.01em]"
              style="font-size: {{ max(16, $size * 0.6) }}px;">Triangle POS</span>
    @endif
</div>
