<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <a href="{{ route('adjustments.create') }}" class="btn btn-primary">
                {{ __('adjustment.add_adjustment') }} <i class="bi bi-plus"></i>
            </a>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $adjustments->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($adjustments as $adjustment)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="adjustment-{{ $adjustment->id }}">
                <div class="card h-100">
                    <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 rounded-t-xl">
                        <span class="fw-bold">{{ $adjustment->reference }}</span>
                    </div>
                    <div class="flex-auto p-2">
                        <ul class="list-group list-group-flush mb-0">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('adjustment.date') }}</span><span>{{ $adjustment->date }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('adjustment.products') }}</span><span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-info">{{ $adjustment->adjusted_products_count }}</span></li>
                        </ul>
                    </div>
                    <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            @can('show_adjustments')
                                <a href="{{ route('adjustments.show', $adjustment->id) }}" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-eye me-1 text-info"></i> {{ __('app.details') }}
                                </a>
                            @endcan
                            @can('edit_adjustments')
                                <a href="{{ route('adjustments.edit', $adjustment->id) }}" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-pencil me-1 text-primary"></i> {{ __('app.edit') }}
                                </a>
                            @endcan
                            @can('delete_adjustments')
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $adjustment->id }})" wire:confirm="{{ __('app.are_you_sure') }}">
                                    <i class="bi bi-trash me-1"></i> {{ __('app.delete') }}
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('adjustment.no_adjustments_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $adjustments->links('pagination::bootstrap-5') }}
    </div>
</div>
