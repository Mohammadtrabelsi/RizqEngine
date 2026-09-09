{{-- resources/views/components/notion-card.blade.php --}}
{{--
    Notion-style card.

    A clean, page-like card echoing Notion's database gallery cards: an
    optional cover strip, an emoji/icon glyph, a title, a short excerpt, and
    a footer row for meta + tags. Built on the project's redesign tokens
    (hairline borders, rounded-card, ink/body text) so it drops into any
    Blade view.

    Usage:
        <x-notion-card
            title="Quarterly roadmap"
            excerpt="Planning notes for Q3 delivery and open questions."
            icon="📄"
            cover="linear-gradient(135deg,#eeecfd,#dedaf9)"
            :tags="[['label' => 'Planning', 'tone' => 'accent'], ['label' => 'Draft']]"
            meta="Edited 2h ago"
            href="{{ route('quotation.index') }}"
        />

    Or with slot content instead of the excerpt prop:
        <x-notion-card title="Notes">
            Free-form body content goes here.
        </x-notion-card>
--}}
@props([
    'title',
    'excerpt' => null,
    'icon' => null,
    'cover' => null,
    'tags' => [],
    'meta' => null,
    'href' => null,
])

@php
    // Render as an <a> when a link is given, otherwise a plain <div>.
    $tag = $href ? 'a' : 'div';

    $toneClasses = [
        'accent' => 'bg-accent-light text-accent',
        'ok' => 'bg-ok-bg text-ok',
        'warn' => 'bg-warn-bg text-warn',
        'danger' => 'bg-danger-bg text-danger',
        'neutral' => 'bg-canvas-2 text-ink-3',
    ];
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->class([
        'group block overflow-hidden rounded-card border border-hairline bg-white',
        'transition duration-200 hover:-translate-y-0.5 hover:border-accent-soft hover:shadow-mock',
        'focus:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2',
    ]) }}
>
    @if ($cover)
        <div class="h-24 w-full" style="background: {{ $cover }};"></div>
    @endif

    <div class="p-4">
        @if ($icon)
            <div @class([
                'flex h-9 w-9 items-center justify-center rounded-field text-lg leading-none',
                'bg-canvas-2 ring-1 ring-hairline',
                '-mt-8 mb-3 bg-white shadow-sm' => $cover,
            ])>
                {{ $icon }}
            </div>
        @endif

        <h3 class="text-[15px] font-semibold leading-snug text-ink group-hover:text-accent">
            {{ $title }}
        </h3>

        @if ($excerpt || ! $slot->isEmpty())
            <div class="mt-1.5 line-clamp-3 text-[13px] leading-relaxed text-body">
                {{ $excerpt ?? $slot }}
            </div>
        @endif

        @if (! empty($tags) || $meta)
            <div class="mt-3.5 flex flex-wrap items-center gap-2">
                @foreach ($tags as $t)
                    @php
                        $label = is_array($t) ? ($t['label'] ?? '') : $t;
                        $tone = is_array($t) ? ($t['tone'] ?? 'neutral') : 'neutral';
                    @endphp
                    <span @class([
                        'rounded-full px-[9px] py-[3px] text-[11px] font-semibold',
                        $toneClasses[$tone] ?? $toneClasses['neutral'],
                    ])>{{ $label }}</span>
                @endforeach

                @if ($meta)
                    <span class="ml-auto font-mono text-[11px] text-muted">{{ $meta }}</span>
                @endif
            </div>
        @endif
    </div>
</{{ $tag }}>
