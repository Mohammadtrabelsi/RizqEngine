{{-- resources/views/livewire/landing-page.blade.php --}}
<div>
    <header class="sticky top-0 z-40 flex items-center justify-between gap-8 border-b border-hairline bg-canvas/85 px-6 py-[18px] backdrop-blur-md lg:px-12">
        <a href="{{ route('redesign.landing') }}" class="flex items-center gap-3">
            <x-logo-mark />
            <span class="font-display text-lg font-bold tracking-[-0.01em]">Triangle POS</span>
        </a>

        <nav class="hidden items-center gap-[30px] text-sm font-medium text-ink-3 md:flex">
            <a href="#features" class="transition-colors hover:text-accent">{{ __('nav.features') }}</a>
            <a href="#access" class="transition-colors hover:text-accent">{{ __('nav.access_control') }}</a>
            <a href="#reporting" class="transition-colors hover:text-accent">{{ __('nav.reporting') }}</a>
        </nav>

        <div class="flex items-center gap-2.5">
            <livewire:locale-switcher />
            <a href="{{ route('redesign.login') }}"
               class="rounded-ctl bg-accent px-[18px] py-2.5 text-[13.5px] font-semibold text-white shadow-cta transition-colors hover:bg-accent-hover">
                {{ __('auth.sign_in') }}
            </a>
        </div>
    </header>

    <section class="mx-auto grid max-w-[1440px] grid-cols-1 items-center gap-16 px-6 pb-[88px] pt-16 lg:grid-cols-[1.05fr_0.95fr] lg:px-12 lg:pt-24">
        <div>
            <p class="inline-flex items-center gap-2.5 rounded-full border border-accent-soft bg-accent-light px-3 py-1.5 font-mono text-[11px] font-semibold uppercase tracking-micro text-accent">
                <span class="h-1.5 w-1.5 rounded-full bg-accent"></span>{{ __('landing.eyebrow') }}
            </p>

            <h1 class="mt-6 font-display text-[48px] font-bold leading-[0.98] tracking-display text-balance lg:text-[76px]">
                {{ __('landing.headline') }}
            </h1>

            <p class="mt-[26px] max-w-[520px] text-[17.5px] leading-[1.65] text-body text-pretty">
                {{ __('landing.lead') }}
            </p>

            <div class="mt-9 flex flex-wrap gap-3">
                <a href="{{ route('redesign.login') }}" class="rounded-field bg-ink px-[26px] py-[15px] text-[15px] font-semibold text-white transition-colors hover:bg-ink-2">{{ __('landing.get_started') }}</a>
                <a href="#features" class="rounded-field border border-hairline bg-white px-[26px] py-[15px] text-[15px] font-semibold text-ink transition-colors hover:border-accent hover:text-accent">{{ __('landing.see_features') }}</a>
            </div>

            <dl class="mt-14 flex flex-wrap gap-10 border-t border-hairline pt-7">
                @foreach ($this->stats as $stat)
                    <div>
                        <dd class="font-mono text-[26px] font-semibold tracking-num">{{ $stat['value'] }}</dd>
                        <dt class="mt-1 text-xs font-semibold uppercase tracking-[0.08em] text-muted">{{ $stat['label'] }}</dt>
                    </div>
                @endforeach
            </dl>
        </div>

        {{-- Till mock: static illustration --}}
        <div class="relative">
            <div class="overflow-hidden rounded-[20px] border border-hairline bg-white shadow-mock">
                <div class="flex items-center justify-between border-b border-hairline bg-canvas px-4 py-3">
                    <div class="flex gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full bg-hairline"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-hairline"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-hairline"></span>
                    </div>
                    <span class="font-mono text-[11px] tracking-[0.08em] text-muted">TILL / REGISTER 01</span>
                </div>

                <div class="grid gap-3.5 p-5">
                    <div class="flex items-baseline justify-between">
                        <span class="text-xs font-semibold uppercase tracking-[0.09em] text-muted">{{ __('till.cart_total') }}</span>
                        <span class="font-mono text-[34px] font-semibold tracking-num">184.60<span class="text-[17px] text-muted">DT</span></span>
                    </div>

                    @foreach ($this->mockCart as $line)
                        <div @class([
                            'flex items-center justify-between rounded-field border px-3.5 py-3 text-sm',
                            'border-accent-soft bg-accent-light' => $line['highlight'],
                            'border-hairline' => ! $line['highlight'],
                        ])>
                            <span @class(['font-semibold', 'text-accent' => $line['highlight']])>{{ $line['name'] }}</span>
                            <span @class(['font-mono', 'text-accent' => $line['highlight'], 'text-ink-3' => ! $line['highlight']])>
                                {{ $line['qty'] }} × {{ number_format($line['price'], 2) }}
                            </span>
                        </div>
                    @endforeach

                    <div class="grid grid-cols-3 gap-2 pt-1.5 text-center text-[13px] font-semibold">
                        <span class="rounded-field bg-canvas-2 py-3 text-ink-3">{{ __('till.cash') }}</span>
                        <span class="rounded-field bg-canvas-2 py-3 text-ink-3">{{ __('till.card') }}</span>
                        <span class="rounded-field bg-accent py-3 text-white">{{ __('till.charge') }}</span>
                    </div>
                </div>
            </div>

            <div class="absolute -bottom-[26px] -left-4 rounded-card border border-hairline bg-white px-[18px] py-3.5 shadow-mock lg:-left-7">
                <p class="text-[11px] font-semibold uppercase tracking-[0.09em] text-muted">{{ __('landing.stock_synced') }}</p>
                <p class="mt-1.5 flex items-center gap-2 font-mono text-sm font-semibold">
                    <span class="h-[7px] w-[7px] rounded-full bg-ok"></span>{{ __('landing.realtime') }}
                </p>
            </div>
        </div>
    </section>

    <section id="features" class="mx-auto max-w-[1440px] px-6 pb-24 pt-5 lg:px-12">
        <div class="flex flex-wrap items-end justify-between gap-6 border-b-2 border-ink pb-6">
            <h2 class="font-display text-[30px] font-bold tracking-[-0.03em] lg:text-[38px]">{{ __('landing.features_title') }}</h2>
            <span class="font-mono text-[11px] uppercase tracking-micro text-muted">01 — {{ __('landing.modules') }}</span>
        </div>

        <div class="mt-px grid gap-px bg-hairline sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->features as $i => $feature)
                <article @if (! empty($feature['anchor'])) id="{{ $feature['anchor'] }}" @endif class="bg-canvas px-7 py-8 transition-colors hover:bg-white">
                    <span class="font-mono text-xs font-semibold text-accent">/ {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3 class="mb-2 mt-3.5 font-display text-xl tracking-[-0.01em]">{{ $feature['title'] }}</h3>
                    <p class="text-[14.5px] leading-relaxed text-body">{{ $feature['body'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-[1440px] px-6 pb-24 lg:px-12">
        <div class="flex flex-wrap items-center justify-between gap-12 rounded-panel bg-ink p-8 text-white lg:p-12">
            <div>
                <h2 class="font-display text-white text-[26px] font-bold tracking-[-0.03em] lg:text-[34px]">{{ __('landing.cta_title') }}</h2>
                <p class="mt-3 text-base text-white/70">{{ __('landing.cta_lead') }}</p>
            </div>
            <a href="{{ route('redesign.login') }}" class="rounded-field bg-accent px-7 py-4 text-[15px] font-semibold text-white transition-colors hover:brightness-110">
                {{ __('landing.cta_button') }}
            </a>
        </div>

        <div class="mt-8 flex justify-between text-[13px] text-muted">
            <span>© {{ now()->year }} Triangle POS</span>
            <span class="font-mono">Laravel · Livewire</span>
        </div>
    </section>
</div>
