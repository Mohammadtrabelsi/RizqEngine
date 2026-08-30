{{--
    Triangle POS — shared admin header.
    Redesign shell look (unified with the dashboard): page title + date subline,
    language switcher, POS shortcut, low-stock notifications and a user menu.
    The `low_quantity_products` collection is injected by a view composer in
    AppServiceProvider. `title` / `subtitle` are passed in by the layout shell.
--}}
@php
    $headerTitle = $title ?? trim($__env->yieldContent('title')) ?: config('app.name');
    $headerSubtitle = $subtitle ?? (now()->isoFormat('dddd, D MMMM YYYY') . ' · ' . __('dash.all_registers'));
@endphp

<header class="sticky top-0 z-30 flex flex-wrap items-center justify-between gap-4 border-b border-hairline bg-canvas px-5 py-4 lg:px-8">
    <div class="flex items-center gap-3">
        <button type="button"
                class="grid h-[38px] w-[38px] place-items-center rounded-ctl border border-hairline bg-white lg:hidden"
                data-toggle="collapse" data-target="#app-sidebar"
                aria-controls="app-sidebar" aria-expanded="false" aria-label="{{ __('menu.products') }}">
            <svg class="h-4 w-4 text-ink-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="min-w-0">
            <h1 class="truncate font-display text-[22px] font-bold tracking-[-0.02em]">{{ $headerTitle }}</h1>
            <p class="mt-0.5 text-[13px] text-body">{{ $headerSubtitle }}</p>
        </div>
    </div>

    <div class="flex items-center gap-2.5">
        <livewire:locale-switcher />

        @can('create_pos_sales')
            <a href="{{ route('app.pos.index') }}"
               @class([
                   'hidden rounded-ctl bg-accent px-[18px] py-2.5 text-[13.5px] font-semibold text-white transition-colors hover:bg-accent-hover sm:inline-block',
                   'pointer-events-none opacity-60' => request()->routeIs('app.pos.index'),
               ])>
                {{ __('dash.open_pos') }}
            </a>
        @endcan

        @can('show_notifications')
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" @click.outside="open = false"
                        class="relative grid h-[38px] w-[38px] place-items-center rounded-ctl border border-hairline bg-white text-ink-3 transition-colors hover:text-accent">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    @if(($low_quantity_products ?? collect())->count() > 0)
                        <span class="absolute -right-1.5 -top-1.5 rounded-full bg-danger px-1.5 font-mono text-[10px] font-semibold text-white">
                            {{ $low_quantity_products->count() > 9 ? '9+' : $low_quantity_products->count() }}
                        </span>
                    @endif
                </button>

                <div x-show="open" x-cloak x-transition
                     class="absolute end-0 z-40 mt-2 w-[340px] overflow-hidden rounded-card border border-hairline bg-white shadow-xl">
                    <div class="flex items-center justify-between gap-2 border-b border-hairline px-4 py-3.5">
                        <div>
                            <p class="font-display text-sm font-bold">{{ __('app.notifications') }}</p>
                            <p class="text-[12px] text-body">{{ __('app.low-stock-alerts') }}</p>
                        </div>
                        @if(($low_quantity_products ?? collect())->count() > 0)
                            <span class="rounded-full bg-warn-bg px-2 py-0.5 font-mono text-[11px] font-semibold text-warn">
                                {{ $low_quantity_products->count() }} {{ __('app.alerts') }}
                            </span>
                        @else
                            <span class="rounded-full bg-ok-bg px-2 py-0.5 font-mono text-[11px] font-semibold text-ok">{{ __('app.all-clear') }}</span>
                        @endif
                    </div>

                    <div class="max-h-[320px] overflow-y-auto">
                        @forelse(($low_quantity_products ?? collect()) as $product)
                            <a href="{{ route('products.show', $product->id) }}"
                               class="flex items-center gap-3 border-b border-hairline px-4 py-3 transition-colors hover:bg-canvas">
                                <span @class([
                                    'grid h-9 w-9 shrink-0 place-items-center rounded-ctl',
                                    'bg-warn-bg text-warn' => $product->product_quantity <= $product->product_stock_alert,
                                    'bg-ok-bg text-ok' => $product->product_quantity > $product->product_stock_alert,
                                ])>
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[13.5px] font-semibold">{{ $product->product_name }}</p>
                                    <p class="font-mono text-[11.5px] text-muted">{{ $product->product_code }}</p>
                                    @php
                                        $notifKey = match(true) {
                                            $product->product_quantity <= 0 => 'app.notif-desc-out',
                                            $product->product_quantity <= $product->product_stock_alert => 'app.notif-desc-critical',
                                            default => 'app.notif-desc-low',
                                        };
                                    @endphp
                                    <p class="mt-1 text-[11.5px] leading-snug text-body">
                                        {{ __($notifKey, [
                                            'qty' => $product->product_quantity,
                                            'unit' => $product->product_unit,
                                            'alert' => $product->product_stock_alert,
                                        ]) }}
                                    </p>
                                </div>
                                <span @class([
                                    'font-mono text-sm font-semibold',
                                    'text-danger' => $product->product_quantity <= $product->product_stock_alert,
                                    'text-warn' => $product->product_quantity > $product->product_stock_alert,
                                ])>{{ $product->product_quantity }}</span>
                            </a>
                        @empty
                            <div class="px-4 py-8 text-center">
                                <p class="text-[13.5px] font-semibold">{{ __('app.all-good') }}</p>
                                <p class="mt-1 text-[12.5px] text-muted">{{ __('app.no-notifications') }}</p>
                            </div>
                        @endforelse
                    </div>

                    @if(($low_quantity_products ?? collect())->count() > 0)
                        <a href="{{ route('products.index') }}" class="block border-t border-hairline px-4 py-3 text-center text-[13px] font-semibold text-accent hover:text-accent-hover">
                            {{ __('app.view-all-products') }}
                        </a>
                    @endif
                </div>
            </div>
        @endcan

        {{-- User menu --}}
        <div class="relative" x-data="{ open: false }">
            <button type="button" @click="open = !open" @click.outside="open = false"
                    class="flex items-center gap-2.5 rounded-full border border-hairline bg-white py-[5px] pe-3 ps-[5px]">
                <span class="grid h-[30px] w-[30px] place-items-center rounded-full bg-ink font-mono text-xs font-semibold text-white">
                    {{ auth()->user()->initials() }}
                </span>
                <span class="hidden text-start sm:block">
                    <span class="block text-[13px] font-bold leading-tight">{{ auth()->user()->name }}</span>
                    <span class="flex items-center gap-1.5 text-[11.5px] text-ok">
                        <span class="h-[5px] w-[5px] rounded-full bg-ok"></span>{{ __('dash.online') }}
                    </span>
                </span>
                <svg class="hidden h-3.5 w-3.5 text-ink-3 sm:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </button>

            <div x-show="open" x-cloak x-transition
                 class="absolute end-0 z-40 mt-2 w-[260px] overflow-hidden rounded-card border border-hairline bg-white shadow-xl">
                <div class="border-b border-hairline px-4 py-3.5">
                    <p class="truncate text-sm font-bold">{{ auth()->user()->name }}</p>
                    <p class="truncate text-[12.5px] text-body">{{ auth()->user()->email }}</p>
                </div>
                <div class="p-1.5">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 rounded-ctl px-3 py-2.5 text-[13.5px] font-medium text-ink-3 transition-colors hover:bg-canvas hover:text-ink">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        {{ __('app.profile') }}
                    </a>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="flex items-center gap-2.5 rounded-ctl px-3 py-2.5 text-[13.5px] font-medium text-danger transition-colors hover:bg-danger/10">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        {{ __('app.logout') }}
                    </a>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </div>
        </div>
    </div>
</header>
