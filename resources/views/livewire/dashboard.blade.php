{{-- resources/views/livewire/dashboard.blade.php --}}
<div x-data="{ drawer: false }" class="grid min-h-screen grid-cols-1 bg-canvas-2 lg:grid-cols-[260px_1fr]">
    {{-- Desktop sidebar --}}
    <div class="hidden lg:block">
        <x-app-sidebar active="dashboard" :order-count="$this->pendingOrders" />
    </div>

    {{-- Mobile off-canvas drawer --}}
    <div x-show="drawer" x-cloak class="fixed inset-0 z-50 lg:hidden" style="display:none">
        <div class="absolute inset-0 bg-ink/50" @click="drawer = false"></div>
        <div class="absolute inset-y-0 left-0 w-[260px]" @click.outside="drawer = false">
            <x-app-sidebar active="dashboard" :order-count="$this->pendingOrders" />
        </div>
    </div>

    <main class="min-w-0">
        <header class="sticky top-0 z-30 flex flex-wrap items-center justify-between gap-4 border-b border-hairline bg-canvas px-5 py-4 lg:px-8">
            <div class="flex items-center gap-3">
                <button type="button" @click="drawer = true" class="grid h-[38px] w-[38px] place-items-center rounded-ctl border border-hairline bg-white lg:hidden" aria-label="Menu">
                    <svg class="h-4 w-4 text-ink-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="font-display text-[22px] font-bold tracking-[-0.02em]">{{ __('dash.title') }}</h1>
                    <p class="mt-0.5 text-[13px] text-body">
                        {{ now()->isoFormat('dddd, D MMMM YYYY') }} · {{ __('dash.all_registers') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <livewire:locale-switcher />

                @if (\Illuminate\Support\Facades\Route::has('app.pos.index'))
                    <a href="{{ route('app.pos.index') }}" class="hidden rounded-ctl bg-accent px-[18px] py-2.5 text-[13.5px] font-semibold text-white transition-colors hover:bg-accent-hover sm:inline-block">
                        {{ __('dash.open_pos') }}
                    </a>
                @endif

                <button type="button" class="relative grid h-[38px] w-[38px] place-items-center rounded-ctl border border-hairline bg-white">
                    <svg class="h-4 w-4 text-ink-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    @if ($this->unreadCount)
                        <span class="absolute -right-1.5 -top-1.5 rounded-full bg-danger px-1.5 font-mono text-[10px] font-semibold text-white">
                            {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
                        </span>
                    @endif
                </button>

                <div class="flex items-center gap-2.5 rounded-full border border-hairline bg-white py-[5px] pl-[5px] pr-3">
                    <span class="grid h-[30px] w-[30px] place-items-center rounded-full bg-ink font-mono text-xs font-semibold text-white">
                        {{ auth()->user()->initials() }}
                    </span>
                    <span class="hidden sm:block">
                        <span class="block text-[13px] font-bold leading-tight">{{ auth()->user()->name }}</span>
                        <span class="flex items-center gap-1.5 text-[11.5px] text-ok">
                            <span class="h-[5px] w-[5px] rounded-full bg-ok"></span>{{ __('dash.online') }}
                        </span>
                    </span>
                </div>
            </div>
        </header>

        <div class="grid gap-5 px-5 pb-12 pt-7 lg:px-8">
            {{-- 1. Range filter --}}
            <form wire:submit="apply" class="flex flex-wrap items-end gap-3 rounded-card border border-hairline bg-white p-[18px]">
                <div>
                    <label for="from" class="block font-mono text-[10.5px] font-semibold tracking-micro text-body">{{ __('dash.from') }}</label>
                    <input id="from" type="date" wire:model="from"
                           class="mt-[7px] rounded-ctl border border-hairline bg-canvas px-3 py-2.5 font-mono text-sm text-ink">
                </div>
                <div>
                    <label for="to" class="block font-mono text-[10.5px] font-semibold tracking-micro text-body">{{ __('dash.to') }}</label>
                    <input id="to" type="date" wire:model="to"
                           class="mt-[7px] rounded-ctl border border-hairline bg-canvas px-3 py-2.5 font-mono text-sm text-ink">
                </div>

                <div class="ml-1 flex gap-2">
                    <button type="submit" class="rounded-ctl bg-ink px-[18px] py-[11px] text-[13.5px] font-semibold text-white transition-colors hover:bg-ink-2">{{ __('dash.apply') }}</button>
                    <button type="button" wire:click="resetRange" class="rounded-ctl border border-hairline bg-white px-[18px] py-[11px] text-[13.5px] font-semibold text-ink-3 transition-colors hover:border-ink-3">{{ __('dash.reset') }}</button>
                </div>

                <div class="ml-auto flex gap-1.5">
                    @foreach (['today' => __('dash.today'), '7d' => __('dash.seven_days'), 'month' => __('dash.month')] as $key => $label)
                        <button type="button" wire:click="setPreset('{{ $key }}')"
                                @class([
                                    'rounded-full px-3 py-2 text-xs font-semibold transition-colors',
                                    'bg-accent-light text-accent' => $preset === $key,
                                    'bg-canvas-2 text-ink-3 hover:bg-hairline' => $preset !== $key,
                                ])>{{ $label }}</button>
                    @endforeach
                </div>
            </form>

            {{-- 2. KPI row --}}
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-kpi-card :label="__('dash.kpi.sales_today')" :value="$this->money($this->salesToday)"
                            :chip="$this->salesDeltaLabel" chip-tone="ok" :footer="__('dash.kpi.sales_footer', ['count' => $this->salesCount])" />

                <x-kpi-card :label="__('dash.kpi.transactions')" :value="$this->formatInt($this->txCount)"
                            chip="—" chip-tone="neutral" :footer="__('dash.kpi.tx_footer', ['n' => $this->registerCount])" />

                <x-kpi-card tone="warn" :label="__('dash.kpi.low_stock')" :value="$this->formatInt($this->lowStockCount)"
                            :chip="__('dash.action')" chip-tone="warn" :footer="__('dash.kpi.low_stock_footer')" />

                <x-kpi-card :label="__('dash.kpi.expenses_today')" :value="$this->money($this->expensesToday)"
                            :chip="$this->expensesDeltaLabel" chip-tone="neutral" :footer="__('dash.kpi.expenses_footer', ['count' => $this->expenseCount])" />
            </div>

            {{-- 3. Chart + secondary metrics --}}
            <div class="grid gap-4 xl:grid-cols-[1.6fr_1fr]">
                <section class="rounded-card border border-hairline bg-white p-[22px]">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <span class="font-mono text-[10.5px] font-semibold tracking-micro text-body">{{ __('dash.revenue_14d') }}</span>
                            <p class="mt-2.5 font-mono text-[30px] font-semibold tracking-num">{{ $this->money($this->revenueTotal) }}</p>
                        </div>
                        <div class="flex gap-[18px] text-[12.5px] text-body">
                            <span class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-[3px] bg-accent"></span>{{ __('dash.revenue') }}</span>
                            <span class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-[3px] bg-accent-soft"></span>{{ __('dash.cogs') }}</span>
                        </div>
                    </div>

                    <div class="mt-[26px] grid h-[190px] auto-cols-fr grid-flow-col items-end gap-2.5 border-t border-hairline pt-3.5">
                        @foreach ($this->series as $day)
                            <div class="grid content-end gap-[3px]" title="{{ $day['label'] }}">
                                <div class="rounded-t-[5px] bg-accent" style="height: {{ $day['revenue_px'] }}px"></div>
                                <div class="bg-accent-soft" style="height: {{ $day['cogs_px'] }}px"></div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <div class="grid content-start gap-4">
                    <section class="rounded-card border border-hairline bg-white p-5">
                        <span class="font-mono text-[10.5px] font-semibold tracking-micro text-body">{{ __('dash.gross_profit') }}</span>
                        <p class="mt-3 font-mono text-[28px] font-semibold tracking-num">{{ $this->money($this->grossProfit) }}</p>
                        <div class="mt-3.5 h-1.5 overflow-hidden rounded-full bg-canvas-2">
                            <div class="h-full bg-accent" style="width: {{ $this->marginPct }}%"></div>
                        </div>
                        <p class="mt-2 text-[12.5px] text-body">{{ __('dash.margin_note', ['pct' => $this->marginPct]) }}</p>
                    </section>

                    <section class="rounded-card border border-hairline bg-white p-5">
                        <span class="font-mono text-[10.5px] font-semibold tracking-micro text-body">{{ __('dash.receivables') }}</span>
                        <p class="mt-3 font-mono text-[28px] font-semibold tracking-num">{{ $this->money($this->receivables) }}</p>
                        <p class="mt-2.5 text-[12.5px] text-body">{{ __('dash.receivables_note', ['count' => $this->debtorCount]) }}</p>
                    </section>

                    <section class="rounded-card border border-hairline bg-white p-5">
                        <span class="font-mono text-[10.5px] font-semibold tracking-micro text-body">{{ __('dash.supplier_debt') }}</span>
                        <p class="mt-3 font-mono text-[28px] font-semibold tracking-num">{{ $this->money($this->supplierDebt) }}</p>
                        <p class="mt-2.5 text-[12.5px] text-body">{{ __('dash.next_due', ['date' => $this->nextDueDate]) }}</p>
                    </section>
                </div>
            </div>

            {{-- 4. Transactions + restock --}}
            <div class="grid gap-4 xl:grid-cols-[1.6fr_1fr]">
                <section class="overflow-hidden rounded-card border border-hairline bg-white">
                    <div class="flex items-center justify-between border-b border-hairline px-5 py-[18px]">
                        <h2 class="font-display text-base font-bold">{{ __('dash.recent_tx') }}</h2>
                        @if (\Illuminate\Support\Facades\Route::has('sales.index'))
                            <a href="{{ route('sales.index') }}" class="text-[13px] font-semibold text-accent hover:text-accent-hover">{{ __('dash.view_all') }}</a>
                        @endif
                    </div>

                    <div class="grid grid-cols-[1fr_1.2fr_0.8fr_0.9fr] gap-3 bg-canvas px-5 py-[11px] font-mono text-[10.5px] font-semibold tracking-[0.1em] text-body">
                        <span>{{ __('dash.col.ref') }}</span><span>{{ __('dash.col.customer') }}</span>
                        <span>{{ __('dash.col.status') }}</span><span class="text-right">{{ __('dash.col.total') }}</span>
                    </div>

                    @forelse ($this->recentTransactions as $tx)
                        <div class="grid grid-cols-[1fr_1.2fr_0.8fr_0.9fr] items-center gap-3 border-t border-hairline px-5 py-3.5 text-[13.5px]">
                            <span class="font-mono text-ink-3">{{ $tx['reference'] }}</span>
                            <span class="truncate font-semibold">{{ $tx['customer'] }}</span>
                            <span><x-status-pill :status="$tx['status']" /></span>
                            <span @class(['text-right font-mono font-semibold', 'text-danger' => $tx['total'] < 0])>
                                {{ $this->money($tx['total']) }}
                            </span>
                        </div>
                    @empty
                        <p class="border-t border-hairline px-5 py-6 text-center text-[13px] text-muted">{{ __('dash.walk_in') }}</p>
                    @endforelse
                </section>

                <section class="overflow-hidden rounded-card border border-hairline bg-white">
                    <div class="flex items-center justify-between border-b border-hairline px-5 py-[18px]">
                        <h2 class="font-display text-base font-bold">{{ __('dash.restock_queue') }}</h2>
                        @if (\Illuminate\Support\Facades\Route::has('purchases.create'))
                            <a href="{{ route('purchases.create') }}" class="text-[13px] font-semibold text-accent hover:text-accent-hover">{{ __('dash.order') }}</a>
                        @endif
                    </div>

                    @foreach ($this->restockQueue as $item)
                        <div class="flex items-center justify-between gap-3 border-b border-hairline px-5 py-3.5">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold">{{ $item['name'] }}</p>
                                <p class="text-[12.5px] text-muted">{{ __('dash.reorder_at', ['n' => $item['reorder_point']]) }}</p>
                            </div>
                            <span @class([
                                'font-mono text-sm font-semibold',
                                'text-danger' => $item['stock'] < $item['reorder_point'] * 0.5,
                                'text-warn' => $item['stock'] >= $item['reorder_point'] * 0.5,
                            ])>{{ $item['stock'] }}</span>
                        </div>
                    @endforeach

                    @if (\Illuminate\Support\Facades\Route::has('purchases.create'))
                        <div class="px-5 py-4">
                            <a href="{{ route('purchases.create') }}" class="block w-full rounded-ctl border border-hairline bg-canvas py-3 text-center text-[13.5px] font-semibold text-ink transition-colors hover:border-accent hover:text-accent">
                                {{ __('dash.create_po') }}
                            </a>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </main>
</div>
