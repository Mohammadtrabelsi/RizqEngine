{{-- RizqEngine — shared admin footer (unified redesign shell look). --}}
<footer class="mt-auto flex flex-wrap items-center justify-between gap-3 border-t border-hairline bg-canvas px-5 py-4 text-[12.5px] text-body lg:px-8">
    <div class="flex items-center gap-2.5">
        <x-logo-mark tone="light" />
        <span class="font-display text-sm font-bold text-ink">RizqEngine</span>
        <span class="hidden text-muted sm:inline">· {{ __('app.point-of-sale') }}</span>
    </div>

    <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
        <span>&copy; {{ date('Y') }} RizqEngine. {{ __('app.all-rights-reserved') }}</span>
        <span class="text-hairline">•</span>
        <span>
            {{ __('app.developed-by') }}
            <a target="_blank" rel="noopener" href="https://fahimanzam.netlify.app" class="font-semibold text-accent hover:text-accent-hover">Fahim Anzam Dip</a>
            / {{ __('app.developed') }} <strong class="text-ink">Mohammad TRABELSI</strong>
        </span>
    </div>

    <span class="inline-flex items-center gap-1.5 rounded-full border border-hairline bg-white px-2.5 py-1 font-mono text-[11px] font-semibold text-ink-3">
        <span class="h-[5px] w-[5px] rounded-full bg-ok"></span>{{ __('app.version') }} v3.4.0
    </span>
</footer>
