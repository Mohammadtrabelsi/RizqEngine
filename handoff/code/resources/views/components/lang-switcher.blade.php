@php
    // Two-locale toggle. Keeps the current route, swaps only the {locale} param.
    $current = strtoupper(app()->getLocale());
    $other   = app()->getLocale() === 'fr' ? 'en' : 'fr';
    $target  = request()->routeIs('*') && \Illuminate\Support\Facades\Route::currentRouteName()
        ? route(\Illuminate\Support\Facades\Route::currentRouteName(), ['locale' => $other])
        : url('/'.$other);
@endphp

<a href="{{ $target }}"
   {{ $attributes->merge(['class' => 'flex items-center gap-[7px] rounded-[10px] border border-line bg-white px-3 py-2 font-mono text-xs font-semibold text-body transition hover:border-[oklch(0.75_0.02_280)]']) }}>
    {{ $current }}
    <span class="h-1.5 w-1.5 rotate-45 border-b-[1.5px] border-r-[1.5px] border-current -translate-y-px"></span>
</a>
