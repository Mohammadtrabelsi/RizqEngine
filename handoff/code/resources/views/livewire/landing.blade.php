@php $loc = ['locale' => app()->getLocale()]; @endphp
<div>
    {{-- ===== Header ===== --}}
    <header class="sticky top-0 z-40 flex items-center justify-between gap-8 border-b border-line bg-[oklch(0.985_0.004_280/0.86)] px-12 py-[18px] backdrop-blur-[14px]">
        <x-logo :size="30" />
        <nav class="flex items-center gap-[30px] text-sm font-medium text-body">
            <a href="#features" class="hover:text-brand">{{ __('pos.landing.nav.features') }}</a>
            <a href="#access" class="hover:text-brand">{{ __('pos.landing.nav.access') }}</a>
            <a href="#reporting" class="hover:text-brand">{{ __('pos.landing.nav.reporting') }}</a>
        </nav>
        <div class="flex items-center gap-2.5">
            <x-lang-switcher />
            <a href="{{ route('sign-in', $loc) }}" class="rounded-[10px] bg-brand px-[18px] py-2.5 text-[13.5px] font-semibold text-white shadow-brand transition hover:bg-brand-600">{{ __('pos.landing.sign_in') }}</a>
        </div>
    </header>

    {{-- ===== Hero ===== --}}
    <section class="mx-auto grid max-w-[1440px] grid-cols-[1.05fr_0.95fr] items-center gap-16 px-12 pb-[88px] pt-24">
        <div class="animate-rise">
            <div class="inline-flex items-center gap-2.5 rounded-full border border-[oklch(0.88_0.03_277)] bg-brand-tint py-1.5 pl-2 pr-3 font-mono text-[11px] font-semibold uppercase tracking-[0.12em] text-[oklch(0.45_0.17_277)]">
                <span class="h-1.5 w-1.5 rounded-full bg-brand"></span>{{ __('pos.landing.eyebrow') }}
            </div>
            <h1 class="mt-6 text-balance font-display text-[76px] font-bold leading-[0.98] tracking-[-0.035em]">{{ __('pos.landing.hero_title') }}</h1>
            <p class="mt-[26px] max-w-[520px] text-pretty text-[17.5px] leading-[1.65] text-muted">{{ __('pos.landing.hero_body') }}</p>
            <div class="mt-9 flex gap-3">
                <a href="{{ route('sign-in', $loc) }}" class="rounded-xl2 bg-ink px-[26px] py-[15px] text-[15px] font-semibold text-white transition hover:bg-ink-2">{{ __('pos.landing.get_started') }}</a>
                <a href="#features" class="rounded-xl2 border border-line bg-white px-[26px] py-[15px] text-[15px] font-semibold text-ink transition hover:border-brand hover:text-brand">{{ __('pos.landing.see_features') }}</a>
            </div>
            <div class="mt-14 flex gap-10 border-t border-line pt-7">
                @foreach ([['1 002', 'skus'], ['<150ms', 'till'], ['14', 'roles']] as [$v, $k])
                    <div>
                        <div class="font-mono text-[26px] font-semibold tracking-[-0.02em]">{!! $v !!}</div>
                        <div class="mt-1 text-xs font-semibold uppercase tracking-[0.08em] text-faint">{{ __('pos.landing.stats.'.$k) }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Till mock --}}
        <div class="relative">
            <div class="overflow-hidden rounded-xl3 border border-line bg-white shadow-card">
                <div class="flex items-center justify-between border-b border-line-2 bg-paper px-4 py-3">
                    <div class="flex gap-1.5">
                        <span class="h-[9px] w-[9px] rounded-full bg-[oklch(0.85_0.01_280)]"></span>
                        <span class="h-[9px] w-[9px] rounded-full bg-[oklch(0.85_0.01_280)]"></span>
                        <span class="h-[9px] w-[9px] rounded-full bg-[oklch(0.85_0.01_280)]"></span>
                    </div>
                    <span class="font-mono text-[11px] tracking-[0.08em] text-faint">TILL / REGISTER 01</span>
                </div>
                <div class="grid gap-3.5 p-5">
                    <div class="flex items-baseline justify-between">
                        <span class="text-xs font-semibold uppercase tracking-[0.09em] text-faint">{{ __('pos.landing.cart_total') }}</span>
                        <span class="font-mono text-[34px] font-semibold tracking-[-0.03em]">184.60<span class="text-[17px] text-faint">DT</span></span>
                    </div>
                    <div class="grid gap-2">
                        <div class="flex items-center justify-between rounded-xl border border-line-2 px-3.5 py-3 text-sm"><span class="font-semibold">Espresso beans 1kg</span><span class="font-mono text-muted">2 × 42.00</span></div>
                        <div class="flex items-center justify-between rounded-xl border border-line-2 px-3.5 py-3 text-sm"><span class="font-semibold">Paper cups 8oz (50)</span><span class="font-mono text-muted">4 × 12.15</span></div>
                        <div class="flex items-center justify-between rounded-xl border border-[oklch(0.88_0.03_277)] bg-[oklch(0.97_0.015_277)] px-3.5 py-3 text-sm"><span class="font-semibold text-[oklch(0.4_0.17_277)]">Filter papers V60</span><span class="font-mono text-[oklch(0.45_0.17_277)]">1 × 51.90</span></div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 pt-1.5">
                        <div class="rounded-xl bg-[oklch(0.96_0.005_280)] p-3 text-center text-[13px] font-semibold text-body">{{ __('pos.landing.cash') }}</div>
                        <div class="rounded-xl bg-[oklch(0.96_0.005_280)] p-3 text-center text-[13px] font-semibold text-body">{{ __('pos.landing.card') }}</div>
                        <div class="rounded-xl bg-brand p-3 text-center text-[13px] font-semibold text-white">{{ __('pos.landing.charge') }}</div>
                    </div>
                </div>
            </div>
            <div class="absolute -bottom-[26px] -left-7 rounded-xl2 border border-line bg-white px-[18px] py-3.5 shadow-card">
                <div class="text-[11px] font-semibold uppercase tracking-[0.09em] text-faint">{{ __('pos.landing.stock_synced') }}</div>
                <div class="mt-1.5 flex items-center gap-2"><span class="h-[7px] w-[7px] rounded-full bg-success"></span><span class="font-mono text-sm font-semibold">real-time</span></div>
            </div>
        </div>
    </section>

    {{-- ===== Feature grid ===== --}}
    <section id="features" class="mx-auto max-w-[1440px] px-12 pb-24 pt-5">
        <div class="flex items-end justify-between gap-10 border-b-2 border-ink pb-6">
            <h2 class="font-display text-[38px] font-bold tracking-[-0.03em]">{{ __('pos.landing.features_title') }}</h2>
            <span class="font-mono text-[11px] uppercase tracking-[0.12em] text-faint">01 — {{ __('pos.landing.features_label') }}</span>
        </div>
        <div class="mt-px grid grid-cols-3 gap-px bg-line">
            @foreach ($this->modules() as $m)
                <div @isset($m['anchor']) id="{{ $m['anchor'] }}" @endisset class="bg-paper p-8 transition hover:bg-white">
                    <span class="font-mono text-xs font-semibold text-brand">/ {{ $m['index'] }}</span>
                    <h3 class="mb-2 mt-3.5 font-display text-xl tracking-[-0.01em]">{{ $m['title'] }}</h3>
                    <p class="text-[14.5px] leading-[1.6] text-muted">{{ $m['body'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ===== CTA + footer ===== --}}
    <section class="mx-auto max-w-[1440px] px-12 pb-24">
        <div class="flex items-center justify-between gap-12 rounded-[22px] bg-ink p-12 text-white">
            <div>
                <h2 class="font-display text-[34px] font-bold tracking-[-0.03em]">{{ __('pos.landing.cta_title') }}</h2>
                <p class="mt-3 text-base text-[oklch(0.8_0.01_280)]">{{ __('pos.landing.cta_body') }}</p>
            </div>
            <a href="{{ route('sign-in', $loc) }}" class="flex-none rounded-xl2 bg-brand-400 px-7 py-4 text-[15px] font-semibold text-white transition hover:bg-brand-300">{{ __('pos.landing.cta_button') }}</a>
        </div>
        <div class="mt-8 flex justify-between text-[13px] text-faint">
            <span>© 2026 Triangle POS</span>
            <span class="font-mono">Laravel · Livewire</span>
        </div>
    </section>
</div>
