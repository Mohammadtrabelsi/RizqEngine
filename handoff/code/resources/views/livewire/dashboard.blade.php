<div>
    {{-- ===== Top bar ===== --}}
    <header class="sticky top-0 z-30 flex items-center justify-between gap-6 border-b border-line bg-paper px-8 py-4">
        <div>
            <h1 class="font-display text-[22px] font-bold tracking-[-0.02em]">{{ __('pos.nav.dashboard') }}</h1>
            <div class="mt-0.5 text-[13px] text-muted">{{ \Carbon\Carbon::parse($to)->translatedFormat('l, j F Y') }} · {{ __('pos.dash.all_registers') }}</div>
        </div>
        <div class="flex items-center gap-2.5">
            <x-lang-switcher />
            <a href="#pos" class="rounded-[10px] bg-brand px-[18px] py-2.5 text-[13.5px] font-semibold text-white transition hover:bg-brand-600">{{ __('pos.dash.open_pos') }}</a>
            <div class="relative grid h-[38px] w-[38px] place-items-center rounded-[10px] border border-line bg-white">
                <span class="h-[13px] w-[13px] rounded-[4px_4px_6px_6px] border-[1.6px] border-body"></span>
                <span class="absolute -right-1.5 -top-1.5 rounded-full bg-[oklch(0.55_0.2_20)] px-[5px] py-px font-mono text-[10px] font-semibold text-white">9+</span>
            </div>
            <div class="flex items-center gap-2.5 rounded-full border border-line bg-white py-[5px] pl-[5px] pr-3">
                <div class="grid h-[30px] w-[30px] place-items-center rounded-full bg-ink font-mono text-xs font-semibold text-white">AD</div>
                <div>
                    <div class="text-[13px] font-bold leading-[1.1]">Administrator</div>
                    <div class="flex items-center gap-1.5 text-[11.5px] text-success-fg"><span class="h-[5px] w-[5px] rounded-full bg-success"></span>{{ __('pos.dash.online') }}</div>
                </div>
            </div>
        </div>
    </header>

    <div class="grid gap-5 px-8 pb-12 pt-7">
        {{-- ===== Filter toolbar ===== --}}
        <div class="flex flex-wrap items-end gap-3 rounded-xl2 border border-line bg-white px-4.5 py-4">
            <div>
                <label class="block font-mono text-[10.5px] font-semibold tracking-[0.12em] text-muted">{{ __('pos.dash.from') }}</label>
                <input type="date" wire:model="from" class="mt-1.5 rounded-[10px] border border-line bg-paper px-3 py-2.5 font-mono text-sm font-medium text-ink">
            </div>
            <div>
                <label class="block font-mono text-[10.5px] font-semibold tracking-[0.12em] text-muted">{{ __('pos.dash.to') }}</label>
                <input type="date" wire:model="to" class="mt-1.5 rounded-[10px] border border-line bg-paper px-3 py-2.5 font-mono text-sm font-medium text-ink">
            </div>
            <div class="ml-1 flex gap-2">
                <button wire:click="$refresh" class="rounded-[10px] bg-ink px-4.5 py-2.5 text-[13.5px] font-semibold text-white transition hover:bg-ink-2">{{ __('pos.dash.apply') }}</button>
                <button wire:click="resetRange" class="rounded-[10px] border border-line bg-white px-4.5 py-2.5 text-[13.5px] font-semibold text-body transition hover:border-[oklch(0.75_0.02_280)]">{{ __('pos.dash.reset') }}</button>
            </div>
            <div class="ml-auto flex gap-1.5">
                <button wire:click="range('today')" class="rounded-full bg-brand-tint px-3 py-2 text-xs font-semibold text-[oklch(0.45_0.17_277)]">{{ __('pos.dash.today') }}</button>
                <button wire:click="range('7days')" class="rounded-full bg-[oklch(0.96_0.005_280)] px-3 py-2 text-xs font-semibold text-muted">{{ __('pos.dash.7days') }}</button>
                <button wire:click="range('month')" class="rounded-full bg-[oklch(0.96_0.005_280)] px-3 py-2 text-xs font-semibold text-muted">{{ __('pos.dash.month') }}</button>
            </div>
        </div>

        {{-- ===== KPI tiles ===== --}}
        <div class="grid grid-cols-4 gap-4">
            @foreach ($this->stats() as $s)
                @php
                    $warn = $s['tone'] === 'warn';
                    $delta = match ($s['tone']) {
                        'up' => 'bg-success-bg text-success-fg',
                        'warn' => 'bg-[oklch(0.94_0.08_70)] text-warn',
                        default => 'bg-[oklch(0.96_0.005_280)] text-muted',
                    };
                @endphp
                <div class="rounded-xl2 border p-5 {{ $warn ? 'border-[oklch(0.88_0.05_60)] bg-[oklch(0.99_0.02_75)]' : 'border-line bg-white' }}">
                    <div class="flex items-start justify-between">
                        <span class="font-mono text-[10.5px] font-semibold tracking-[0.12em] {{ $warn ? 'text-[oklch(0.48_0.1_60)]' : 'text-muted' }}">{{ __('pos.dash.kpi.'.$s['key']) }}</span>
                        <span class="rounded-md px-[7px] py-[3px] font-mono text-[11px] font-semibold {{ $delta }}">{{ $s['delta'] }}</span>
                    </div>
                    <div class="mt-3.5 font-mono text-[32px] font-semibold tracking-[-0.03em] {{ $warn ? 'text-[oklch(0.4_0.11_55)]' : '' }}">
                        {{ $s['value'] }}@isset($s['unit'])<span class="text-[15px] text-faint">{{ $s['unit'] }}</span>@endisset
                    </div>
                    <div class="mt-2.5 text-[13px] {{ $warn ? 'text-[oklch(0.48_0.07_60)]' : 'text-muted' }}">{{ $s['note'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- ===== Revenue chart + side stats ===== --}}
        <div class="grid grid-cols-[1.6fr_1fr] gap-4">
            <div class="rounded-xl2 border border-line bg-white p-5.5">
                <div class="flex items-end justify-between">
                    <div>
                        <span class="font-mono text-[10.5px] font-semibold tracking-[0.12em] text-muted">{{ __('pos.dash.revenue_14') }}</span>
                        <div class="mt-2.5 font-mono text-[30px] font-semibold tracking-[-0.03em]">31 244.10<span class="text-[15px] text-faint">DT</span></div>
                    </div>
                    <div class="flex gap-4.5 text-[12.5px] text-muted">
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-[3px] bg-brand"></span>{{ __('pos.dash.revenue') }}</span>
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-[3px] bg-[oklch(0.88_0.03_277)]"></span>COGS</span>
                    </div>
                </div>
                <div class="mt-6.5 grid h-[190px] grid-flow-col auto-cols-fr items-end gap-2.5 border-t border-line-2 pt-3.5">
                    @foreach ($this->chart() as [$rev, $cogs])
                        <div class="grid content-end gap-[3px]">
                            <div class="rounded-t-[5px] bg-brand" style="height: {{ $rev }}px"></div>
                            <div class="bg-[oklch(0.9_0.03_277)]" style="height: {{ $cogs }}px"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid content-start gap-4">
                <div class="rounded-xl2 border border-line bg-white p-5">
                    <span class="font-mono text-[10.5px] font-semibold tracking-[0.12em] text-muted">{{ __('pos.dash.gross_profit') }}</span>
                    <div class="mt-3 font-mono text-[28px] font-semibold tracking-[-0.03em]">9 812.40<span class="text-sm text-faint">DT</span></div>
                    <div class="mt-3.5 h-1.5 overflow-hidden rounded-full bg-line-2"><div class="h-full bg-brand" style="width:31%"></div></div>
                    <div class="mt-2 text-[12.5px] text-muted">{{ __('pos.dash.margin_note') }}</div>
                </div>
                <div class="rounded-xl2 border border-line bg-white p-5">
                    <span class="font-mono text-[10.5px] font-semibold tracking-[0.12em] text-muted">{{ __('pos.dash.receivables') }}</span>
                    <div class="mt-3 font-mono text-[28px] font-semibold tracking-[-0.03em]">9 765.23<span class="text-sm text-faint">DT</span></div>
                    <div class="mt-2.5 text-[12.5px] text-muted">{{ __('pos.dash.receivables_note') }}</div>
                </div>
                <div class="rounded-xl2 border border-line bg-white p-5">
                    <span class="font-mono text-[10.5px] font-semibold tracking-[0.12em] text-muted">{{ __('pos.dash.supplier_debt') }}</span>
                    <div class="mt-3 font-mono text-[28px] font-semibold tracking-[-0.03em]">4 120.00<span class="text-sm text-faint">DT</span></div>
                    <div class="mt-2.5 text-[12.5px] text-muted">{{ __('pos.dash.debt_note') }}</div>
                </div>
            </div>
        </div>

        {{-- ===== Recent transactions + restock queue ===== --}}
        <div class="grid grid-cols-[1.6fr_1fr] gap-4">
            <div class="overflow-hidden rounded-xl2 border border-line bg-white">
                <div class="flex items-center justify-between border-b border-line-2 px-5 py-4.5">
                    <span class="font-display text-base font-bold">{{ __('pos.dash.recent_tx') }}</span>
                    <a href="#transactions" class="text-[13px] font-semibold text-brand">{{ __('pos.dash.view_all') }}</a>
                </div>
                <div class="grid grid-cols-[1fr_1.2fr_0.8fr_0.9fr] gap-3 bg-paper px-5 py-2.5 font-mono text-[10.5px] font-semibold tracking-[0.1em] text-muted">
                    <span>REF</span><span>{{ __('pos.dash.customer') }}</span><span>{{ __('pos.dash.status') }}</span><span class="text-right">{{ __('pos.dash.total') }}</span>
                </div>
                @foreach ([
                    ['SL-2418', 'Café Nour', 'paid', '412.00DT', false],
                    ['SL-2417', 'Walk-in', 'paid', '86.50DT', false],
                    ['SL-2416', 'Épicerie Salah', 'partial', '1 240.00DT', false],
                    ['RT-0112', 'Hôtel Amilcar', 'return', '-118.00DT', true],
                    ['SL-2415', 'Boulangerie Ons', 'paid', '639.40DT', false],
                ] as [$ref, $cust, $status, $total, $neg])
                    <div class="grid grid-cols-[1fr_1.2fr_0.8fr_0.9fr] items-center gap-3 border-t border-line-2 px-5 py-3.5 text-[13.5px]">
                        <span class="font-mono text-body">{{ $ref }}</span>
                        <span class="font-semibold">{{ $cust }}</span>
                        <span><x-status-badge :status="$status" /></span>
                        <span class="text-right font-mono font-semibold {{ $neg ? 'text-danger' : '' }}">{{ $total }}</span>
                    </div>
                @endforeach
            </div>

            <div class="overflow-hidden rounded-xl2 border border-line bg-white">
                <div class="flex items-center justify-between border-b border-line-2 px-5 py-4.5">
                    <span class="font-display text-base font-bold">{{ __('pos.dash.restock') }}</span>
                    <a href="#products" class="text-[13px] font-semibold text-brand">{{ __('pos.dash.order') }}</a>
                </div>
                @foreach ([
                    ['Espresso beans 1kg', 20, 3, 'text-danger'],
                    ['Paper cups 8oz', 40, 11, 'text-danger'],
                    ['Filter papers V60', 25, 18, 'text-warn'],
                    ['Oat milk 1L', 30, 22, 'text-warn'],
                ] as [$name, $reorder, $qty, $tone])
                    <div class="flex items-center justify-between gap-3 border-b border-line-2 px-5 py-3.5">
                        <div>
                            <div class="text-sm font-semibold">{{ $name }}</div>
                            <div class="text-[12.5px] text-faint">{{ __('pos.dash.reorder_at', ['n' => $reorder]) }}</div>
                        </div>
                        <span class="font-mono text-sm font-semibold {{ $tone }}">{{ $qty }}</span>
                    </div>
                @endforeach
                <div class="p-5">
                    <button class="w-full rounded-[10px] border border-line bg-paper p-3 text-[13.5px] font-semibold text-ink transition hover:border-brand hover:text-brand">{{ __('pos.dash.create_po') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
