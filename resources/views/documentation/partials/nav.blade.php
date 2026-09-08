{{--
    Shared documentation navigation. Lists every topic grouped by category and
    highlights the active one. Expects $navGroups (from DocumentationController)
    and an optional $current slug.
--}}
@php($current = $current ?? null)

<nav aria-label="{{ __('documentation.in_this_guide') }}" class="space-y-6">
    <a href="{{ route('documentation.index') }}"
       @class([
           'flex items-center gap-2 text-sm font-medium',
           'text-accent' => is_null($current),
           'text-muted hover:text-accent' => ! is_null($current),
       ])>
        <i class="bi bi-grid-1x2"></i>
        {{ __('documentation.overview_heading') }}
    </a>

    @foreach ($navGroups as $group)
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-ink-3">
                {{ $group['label'] }}
            </p>
            <ul class="space-y-0.5">
                @foreach ($group['sections'] as $section)
                    @php($active = $current === $section['key'])
                    <li>
                        <a href="{{ route('documentation.show', $section['key']) }}"
                           @class([
                               'flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition-colors',
                               'bg-accent-light font-semibold text-accent' => $active,
                               'text-ink hover:bg-canvas-2' => ! $active,
                           ])
                           @if ($active) aria-current="page" @endif>
                            <i class="bi {{ $section['icon'] }} text-base"></i>
                            <span>{{ $section['title'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</nav>
