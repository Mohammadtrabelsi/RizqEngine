@php
    $settingsTabs = [
        ['route' => 'settings.general', 'label' => __('settings.general_settings')],
        ['route' => 'settings.mail', 'label' => __('settings.mail_settings')],
        ['route' => 'settings.images', 'label' => __('settings.default_product_image')],
    ];
@endphp

<nav class="mb-4 flex flex-wrap items-center gap-1 rounded-ctl border border-hairline bg-white p-1"
     aria-label="{{ __('settings.settings') }}">
    @foreach ($settingsTabs as $tab)
        <a href="{{ route($tab['route']) }}"
           @class([
               'rounded-[7px] px-4 py-2 text-sm font-semibold transition-colors',
               'bg-accent text-white' => request()->routeIs($tab['route']),
               'text-ink-3 hover:bg-canvas hover:text-accent' => ! request()->routeIs($tab['route']),
           ])
           @if(request()->routeIs($tab['route'])) aria-current="page" @endif>
            {{ $tab['label'] }}
        </a>
    @endforeach
</nav>
