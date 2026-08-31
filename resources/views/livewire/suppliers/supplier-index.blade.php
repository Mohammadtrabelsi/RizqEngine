{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                {{ __('supplier.create_supplier') }} <i class="bi bi-plus"></i>
            </a>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    {{-- Filters container --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="card-title text-muted mb-3">
                <i class="bi bi-funnel"></i> {{ __('app.filters') }}
            </h6>
            <div class="row align-items-end">
                <div class="col-12 col-md-3 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('supplier.city') }}</label>
                    <select wire:model.live="city" class="form-select">
                        <option value="">{{ __('app.all') }}</option>
                        @foreach($cities as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('supplier.country') }}</label>
                    <select wire:model.live="country" class="form-select">
                        <option value="">{{ __('app.all') }}</option>
                        @foreach($countries as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('supplier.tax_identification_number') }}</label>
                    <select wire:model.live="hasTaxId" class="form-select">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="yes">{{ __('app.yes') }}</option>
                        <option value="no">{{ __('app.no') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle"></i> {{ __('app.reset') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $suppliers->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($suppliers as $supplier)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="supplier-{{ $supplier->id }}">
                <div class="card h-100">
                    <div class="position-relative overflow-hidden rounded-top media-thumb">
                        @if ($supplier->image_url)
                            <img src="{{ $supplier->image_url }}" class="w-100 h-100 thumb-cover" alt="{{ $supplier->supplier_name }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center w-100 h-100 text-muted">
                                <i class="bi bi-person-circle thumb-placeholder-icon"></i>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $supplier->supplier_name }}</h5>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item px-0"><i class="bi bi-envelope"></i> {{ $supplier->supplier_email }}</li>
                            <li class="list-group-item px-0"><i class="bi bi-telephone"></i> {{ $supplier->supplier_phone }}</li>
                            @if ($supplier->tax_identification_number)
                                <li class="list-group-item px-0"><i class="bi bi-receipt"></i> {{ $supplier->tax_identification_number }}</li>
                            @endif
                        </ul>
                        <div class="btn-group">
                            @can('edit_suppliers')
                                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('show_suppliers')
                                <a href="{{ route('suppliers.show', $supplier->id) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                            @endcan
                            @can('delete_suppliers')
                                <button type="button" class="btn btn-outline-danger btn-sm" wire:click="delete({{ $supplier->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-muted">{{ __('supplier.no_suppliers_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $suppliers->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>
