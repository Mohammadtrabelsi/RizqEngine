<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($title ?? 'Dashboard') . ' · Triangle POS' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Manrope:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-paper-2 font-sans text-ink antialiased">
    @php $loc = ['locale' => app()->getLocale()]; @endphp
    <div class="grid min-h-screen grid-cols-[260px_1fr]">

        {{-- ===== Sidebar (shared by every authenticated screen) ===== --}}
        <aside class="flex flex-col gap-[26px] bg-ink px-4 py-[22px] text-white">
            <x-logo :size="28" tone="dark" class="px-2" />

            <div class="grid gap-1">
                <p class="px-2 pb-2 font-mono text-[10.5px] font-semibold tracking-[0.14em] text-faint">{{ __('pos.nav.groups.overview') }}</p>
                <x-nav-link :href="route('dashboard', $loc)" :active="request()->routeIs('dashboard')" dot>{{ __('pos.nav.dashboard') }}</x-nav-link>
                <x-nav-link href="#pos" dot>{{ __('pos.nav.pos') }}</x-nav-link>
            </div>

            <div class="grid gap-1">
                <p class="px-2 pb-2 font-mono text-[10.5px] font-semibold tracking-[0.14em] text-faint">{{ __('pos.nav.groups.transactions') }}</p>
                <x-nav-link href="#products">{{ __('pos.nav.products') }}</x-nav-link>
                <x-nav-link href="#transactions">{{ __('pos.nav.transactions') }}</x-nav-link>
                <x-nav-link href="#quotes">{{ __('pos.nav.quotes') }}</x-nav-link>
                <x-nav-link href="#orders" :badge="12">{{ __('pos.nav.orders') }}</x-nav-link>
                <x-nav-link href="#expenses">{{ __('pos.nav.expenses') }}</x-nav-link>
            </div>

            <div class="grid gap-1">
                <p class="px-2 pb-2 font-mono text-[10.5px] font-semibold tracking-[0.14em] text-faint">{{ __('pos.nav.groups.people') }}</p>
                <x-nav-link href="#customers">{{ __('pos.nav.customers') }}</x-nav-link>
                <x-nav-link href="#suppliers">{{ __('pos.nav.suppliers') }}</x-nav-link>
                <x-nav-link href="#staff">{{ __('pos.nav.staff') }}</x-nav-link>
            </div>

            <div class="grid gap-1">
                <p class="px-2 pb-2 font-mono text-[10.5px] font-semibold tracking-[0.14em] text-faint">{{ __('pos.nav.groups.insight') }}</p>
                <x-nav-link href="#reports">{{ __('pos.nav.reports') }}</x-nav-link>
                <x-nav-link href="#settings">{{ __('pos.nav.settings') }}</x-nav-link>
            </div>

            <div class="mt-auto rounded-[14px] bg-[oklch(0.26_0.02_277)] p-3.5">
                <p class="font-mono text-[10.5px] font-semibold tracking-[0.12em] text-[oklch(0.68_0.02_277)]">{{ __('pos.dash.shift_open') }}</p>
                <p class="mt-1.5 font-mono text-[15px] font-semibold">08:12 — now</p>
                <p class="mt-1 text-[12.5px] text-[oklch(0.75_0.012_280)]">Register 01 · Administrator</p>
            </div>
        </aside>

        <main class="min-w-0">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>
</html>
