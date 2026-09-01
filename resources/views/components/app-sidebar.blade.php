{{-- resources/views/components/app-sidebar.blade.php --}}


<aside {{ $attributes->merge(['class' => 'flex h-full flex-col gap-[26px] bg-ink px-4 py-[22px] text-white']) }}>
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-2">
        <x-logo-mark tone="dark" />
        <span class="font-display text-base font-bold">Triangle POS</span>
    </a>

    @foreach ($groups as $groupKey => $items)
        <nav class="grid gap-1">
            <p class="px-2 pb-2 font-mono text-[10.5px] font-semibold tracking-[0.14em] text-white/45">
                {{ __("nav.group.$groupKey") }}
            </p>

            @foreach ($items as $item)
                @if (\Illuminate\Support\Facades\Route::has($item['route']))
                    <a href="{{ route($item['route']) }}"
                       @class([
                           'flex items-center justify-between gap-3 rounded-ctl px-3 py-2.5 text-sm transition-colors',
                           'bg-accent font-semibold text-white' => $active === $item['key'],
                           'font-medium text-white/80 hover:bg-ink-2 hover:text-white' => $active !== $item['key'],
                       ])>
                        <span class="flex items-center gap-3">
                            @if ($item['dot'] ?? false)
                                <span @class(['h-1.5 w-1.5 rounded-full', 'bg-white' => $active === $item['key'], 'bg-white/30' => $active !== $item['key']])></span>
                            @endif
                            {{ $item['label'] }}
                        </span>

                        @if (! empty($item['badge']))
                            <span class="rounded-full bg-white/10 px-[7px] py-0.5 font-mono text-[11px] font-semibold">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                @endif
            @endforeach
        </nav>
    @endforeach

    <div class="mt-auto rounded-[14px] bg-ink-2 p-3.5">
        <p class="font-mono text-[10.5px] font-semibold tracking-micro text-white/50">{{ __('dash.shift_open') }}</p>
        <p class="mt-1.5 font-mono text-[15px] font-semibold">{{ $shiftStart ?? '08:12' }} — {{ __('dash.now') }}</p>
        <p class="mt-1 text-[12.5px] text-white/60">{{ __('dash.register_role', ['register' => '01', 'role' => auth()->user()->role_label]) }}</p>
    </div>
</aside>
