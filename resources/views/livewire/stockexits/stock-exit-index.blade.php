<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            @can('create_stock_exits')
                <a href="{{ route('stock-exits.create') }}" class="btn btn-primary">
                    {{ __('stockexit.add_exit') }} <i class="bi bi-plus"></i>
                </a>
            @endcan
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    {{-- Filters container --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
            <h5 class="mb-0"><i class="bi bi-funnel text-primary"></i> {{ __('app.filters') }}</h5>
        </div>
        <div class="flex-auto p-2">
            <div class="row align-items-end">
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('stockexit.kind') }}</label>
                    <select wire:model.live="kind" class="form-select">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="{{ \App\Models\StockExit::KIND_STANDARD }}">{{ __('stockexit.kind_standard') }}</option>
                        <option value="{{ \App\Models\StockExit::KIND_CONSIGNMENT }}">{{ __('stockexit.kind_consignment') }}</option>
                    </select>
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('stockexit.line_status') }}</label>
                    <select wire:model.live="status" class="form-select">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="{{ \App\Models\StockExit::STATUS_IN_TRANSIT }}">{{ __('stockexit.status_in_transit') }}</option>
                        <option value="{{ \App\Models\StockExit::STATUS_CLOSED }}">{{ __('stockexit.status_closed') }}</option>
                    </select>
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('reports.start_date') }}</label>
                    <input type="date" wire:model.live="dateFrom" class="form-control">
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('reports.end_date') }}</label>
                    <input type="date" wire:model.live="dateTo" class="form-control">
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <button type="button" wire:click="resetFilters" class="btn btn-secondary w-100">
                        <i class="bi bi-x-circle"></i> {{ __('app.reset') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $stockExits->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($stockExits as $stockExit)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="stock-exit-{{ $stockExit->id }}">
                <div class="card h-100">
                    <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 rounded-t-xl d-flex justify-content-between align-items-center">
                        <span class="fw-bold">{{ $stockExit->reference }}</span>
                        @include('stockexit.partials.status', ['data' => $stockExit])
                    </div>
                    <div class="flex-auto p-2">
                        <ul class="list-group list-group-flush mb-0">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('stockexit.date') }}</span><span>{{ \Illuminate\Support\Carbon::parse($stockExit->date)->format('d M, Y') }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('stockexit.reason') }}</span><span>{{ $stockExit->reason ?: '—' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('stockexit.responsible') }}</span><span>{{ $stockExit->responsible ?: '—' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('stockexit.products') }}</span><span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-info">{{ $stockExit->details_count }}</span></li>
                        </ul>
                    </div>
                    <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            @can('show_stock_exits')
                                <a href="{{ route('stock-exits.show', $stockExit->id) }}" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-eye me-1 text-info"></i> {{ __('app.details') }}
                                </a>
                            @endcan
                            @can('create_stock_entries')
                                @if($stockExit->status !== \App\Models\StockExit::STATUS_CLOSED)
                                    <a href="{{ route('stock-entries.create', $stockExit->id) }}" class="btn btn-sm btn-secondary">
                                        <i class="bi bi-box-arrow-in-down me-1 text-success"></i> {{ __('stockexit.declare_return') }}
                                    </a>
                                @endif
                            @endcan
                            @can('delete_stock_exits')
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $stockExit->id }})" wire:confirm="{{ __('app.are_you_sure') }}">
                                    <i class="bi bi-trash me-1"></i> {{ __('app.delete') }}
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('stockexit.no_exits_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $stockExits->links('pagination::bootstrap-5') }}
    </div>
</div>
