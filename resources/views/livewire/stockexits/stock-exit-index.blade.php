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

    <div class="d-flex justify-content-center mb-3">{{ $stockExits->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($stockExits as $stockExit)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="stock-exit-{{ $stockExit->id }}">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title d-flex justify-content-between align-items-start">
                            <span>{{ $stockExit->reference }}</span>
                            @if($stockExit->status === \App\Models\StockExit::STATUS_CLOSED)
                                <span class="badge bg-success">{{ __('stockexit.status_closed') }}</span>
                            @else
                                <span class="badge bg-warning text-dark">{{ __('stockexit.status_in_transit') }}</span>
                            @endif
                        </h5>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('stockexit.date') }}</span><span>{{ \Illuminate\Support\Carbon::parse($stockExit->date)->format('d M, Y') }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('stockexit.reason') }}</span><span>{{ $stockExit->reason ?: '—' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('stockexit.responsible') }}</span><span>{{ $stockExit->responsible ?: '—' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('stockexit.products') }}</span><span class="badge bg-info">{{ $stockExit->details_count }}</span></li>
                        </ul>
                        <div class="btn-group">
                            @can('show_stock_exits')
                                <a href="{{ route('stock-exits.show', $stockExit->id) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                            @endcan
                            @can('create_stock_entries')
                                @if($stockExit->status !== \App\Models\StockExit::STATUS_CLOSED)
                                    <a href="{{ route('stock-entries.create', $stockExit->id) }}" class="btn btn-success btn-sm" title="{{ __('stockexit.declare_return') }}"><i class="bi bi-box-arrow-in-down"></i></a>
                                @endif
                            @endcan
                            @can('delete_stock_exits')
                                <button type="button" class="btn btn-outline-danger btn-sm" wire:click="delete({{ $stockExit->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-muted">{{ __('stockexit.no_exits_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $stockExits->links('pagination::bootstrap-5') }}
    </div>
</div>
