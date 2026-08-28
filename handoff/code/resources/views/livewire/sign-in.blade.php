<div class="grid min-h-screen grid-cols-[1.1fr_1fr]">
    {{-- ===== Brand panel ===== --}}
    <div class="relative flex flex-col justify-between overflow-hidden bg-ink px-16 py-14 text-white">
        <x-logo :size="30" tone="dark" />
        <div class="max-w-[560px]">
            <h1 class="font-display text-[60px] font-bold leading-none tracking-[-0.035em]">{{ __('pos.auth.panel_title') }}</h1>
            <p class="mt-6 text-pretty text-[17px] leading-[1.65] text-[oklch(0.78_0.012_280)]">{{ __('pos.auth.panel_body') }}</p>
            <div class="mt-9 flex gap-2.5">
                @foreach (['multi_store', 'offline_till', 'audit_trail'] as $tag)
                    <span class="rounded-full border border-[oklch(0.35_0.02_277)] px-3.5 py-2 font-mono text-[11px] font-medium tracking-[0.1em] text-[oklch(0.82_0.01_280)]">{{ __('pos.auth.tags.'.$tag) }}</span>
                @endforeach
            </div>
        </div>
        <span class="text-[13px] text-[oklch(0.62_0.015_280)]">© 2026 Triangle POS</span>
    </div>

    {{-- ===== Form ===== --}}
    <div class="grid place-items-center bg-paper px-12 py-14">
        <div class="w-full max-w-[420px]">
            <div class="flex justify-end"><x-lang-switcher /></div>

            <h2 class="mb-2 mt-7 font-display text-[40px] font-bold tracking-[-0.03em]">{{ __('pos.auth.sign_in') }}</h2>
            <p class="mb-8 text-[15px] text-muted">{{ __('pos.auth.welcome') }}</p>

            <form wire:submit="authenticate" class="grid gap-0">
                <label class="text-xs font-bold uppercase tracking-[0.08em] text-body">{{ __('pos.auth.email') }}</label>
                <input type="email" wire:model="email" placeholder="you@store.com" autocomplete="username"
                       class="mt-2 rounded-xl border border-line bg-white px-4 py-3.5 text-[15px] font-medium text-ink outline-none transition focus:border-brand focus:shadow-[0_0_0_4px_oklch(0.52_0.19_277/0.14)]">
                @error('email') <span class="mt-1.5 text-[13px] text-danger">{{ $message }}</span> @enderror

                <div class="mt-5 flex items-baseline justify-between">
                    <label class="text-xs font-bold uppercase tracking-[0.08em] text-body">{{ __('pos.auth.password') }}</label>
                    <a href="#forgot" class="text-[13px] font-semibold text-brand">{{ __('pos.auth.forgot') }}</a>
                </div>
                <input type="password" wire:model="password" placeholder="••••••••" autocomplete="current-password"
                       class="mt-2 rounded-xl border border-line bg-white px-4 py-3.5 text-[15px] font-medium text-ink outline-none transition focus:border-brand focus:shadow-[0_0_0_4px_oklch(0.52_0.19_277/0.14)]">
                @error('password') <span class="mt-1.5 text-[13px] text-danger">{{ $message }}</span> @enderror

                <label class="mt-5 flex cursor-pointer items-center gap-2.5 text-sm text-body">
                    <input type="checkbox" wire:model="remember" class="h-[17px] w-[17px] accent-brand">
                    {{ __('pos.auth.remember') }}
                </label>

                <button type="submit" class="mt-7 rounded-xl bg-brand p-4 text-[15.5px] font-semibold text-white shadow-brand transition hover:bg-brand-600">
                    <span wire:loading.remove wire:target="authenticate">{{ __('pos.auth.sign_in') }}</span>
                    <span wire:loading wire:target="authenticate">…</span>
                </button>
            </form>

            <div class="my-7 flex items-center gap-3.5">
                <span class="h-px flex-1 bg-line"></span>
                <span class="font-mono text-[11px] tracking-[0.1em] text-faint">{{ __('pos.auth.or') }}</span>
                <span class="h-px flex-1 bg-line"></span>
            </div>
            <button type="button" class="w-full rounded-xl border border-line bg-white p-3.5 text-[14.5px] font-semibold text-ink transition hover:border-[oklch(0.75_0.02_280)]">{{ __('pos.auth.use_pin') }}</button>
            <p class="mt-7 text-center text-sm text-muted">{{ __('pos.auth.need_access') }} <a href="#owner" class="font-semibold text-brand">{{ __('pos.auth.contact_owner') }}</a></p>
        </div>
    </div>
</div>
