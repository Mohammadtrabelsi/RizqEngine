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

    <div class="d-flex justify-content-center mb-3">{{ $adjustments->links() }}</div>
    <div class="row">
        @forelse($adjustments as $adjustment)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="adjustment-{{ $adjustment->id }}">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $adjustment->reference }}</h5>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('adjustment.date') }}</span><span>{{ $adjustment->date }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('adjustment.products') }}</span><span class="badge bg-info">{{ $adjustment->adjusted_products_count }}</span></li>
                        </ul>
                        <div class="btn-group">
                            @can('edit_adjustments')
                                <a href="{{ route('adjustments.edit', $adjustment->id) }}" class="btn btn-info btn-sm"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('show_adjustments')
                                <a href="{{ route('adjustments.show', $adjustment->id) }}" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i></a>
                            @endcan
                            @can('delete_adjustments')
                                <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $adjustment->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-muted">{{ __('adjustment.no_adjustments_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $adjustments->links() }}
    </div>
</div>
