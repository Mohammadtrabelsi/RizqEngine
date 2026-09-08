<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            @can('create_stock_exits')
                <a href="{{ route('stock-exits.create') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                    {{ __('stockexit.add_exit') }} <i class="bi bi-plus"></i>
                </a>
            @endcan
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $stockExits->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($stockExits as $stockExit)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="stock-exit-{{ $stockExit->id }}">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 h-100">
                    <div class="flex-auto p-2">
                        <h5 class="mb-3 text-lg font-semibold text-slate-900 d-flex justify-content-between align-items-start">
                            <span>{{ $stockExit->reference }}</span>
                            @if($stockExit->status === \App\Models\StockExit::STATUS_CLOSED)
                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-success">{{ __('stockexit.status_closed') }}</span>
                            @else
                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-warning text-dark">{{ __('stockexit.status_in_transit') }}</span>
                            @endif
                        </h5>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('stockexit.date') }}</span><span>{{ \Illuminate\Support\Carbon::parse($stockExit->date)->format('d M, Y') }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('stockexit.reason') }}</span><span>{{ $stockExit->reason ?: '—' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('stockexit.responsible') }}</span><span>{{ $stockExit->responsible ?: '—' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('stockexit.products') }}</span><span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-info">{{ $stockExit->details_count }}</span></li>
                        </ul>
                        <div class="btn-group">
                            @can('show_stock_exits')
                                <a href="{{ route('stock-exits.show', $stockExit->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-indigo-600 border-indigo-600 hover:bg-indigo-600 hover:!text-white !px-3 !py-1.5 !text-xs"><i class="bi bi-eye"></i></a>
                            @endcan
                            @can('create_stock_entries')
                                @if($stockExit->status !== \App\Models\StockExit::STATUS_CLOSED)
                                    <a href="{{ route('stock-entries.create', $stockExit->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-emerald-500 !text-white border-emerald-500 hover:bg-emerald-600 hover:border-emerald-600 !px-3 !py-1.5 !text-xs" title="{{ __('stockexit.declare_return') }}"><i class="bi bi-box-arrow-in-down"></i></a>
                                @endif
                            @endcan
                            @can('delete_stock_exits')
                                <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-red-500 border-red-500 hover:bg-red-500 hover:!text-white !px-3 !py-1.5 !text-xs" wire:click="delete({{ $stockExit->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900"><div class="flex-auto p-2 text-center text-muted">{{ __('stockexit.no_exits_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $stockExits->links('pagination::bootstrap-5') }}
    </div>
</div>
