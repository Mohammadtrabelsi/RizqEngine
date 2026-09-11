{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                @can('create_warehouses')
                    <a href="{{ route('warehouses.create') }}" class="btn btn-primary">
                        {{ __('warehouses.add_warehouse') }} <i class="bi bi-plus"></i>
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
                    <div class="col-12 col-lg-4 mb-3">
                        <label class="form-label small text-muted mb-1">{{ __('warehouses.status') }}</label>
                        <select wire:model.live="status" class="form-select">
                            <option value="">{{ __('app.all') }}</option>
                            <option value="active">{{ __('warehouses.active') }}</option>
                            <option value="inactive">{{ __('warehouses.inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-12 col-lg-4 mb-3">
                        <label class="form-label small text-muted mb-1">{{ __('warehouses.city') }}</label>
                        <select wire:model.live="city" class="form-select">
                            <option value="">{{ __('app.all') }}</option>
                            @foreach($cities as $cityOption)
                                <option value="{{ $cityOption }}">{{ $cityOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-4 mb-3">
                        <button type="button" wire:click="resetFilters" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> {{ __('app.reset') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mb-3">{{ $warehouses->links('pagination::bootstrap-5') }}</div>

        <div class="row">
            @forelse($warehouses as $warehouse)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="warehouse-{{ $warehouse->id }}">
                    <div class="card h-100">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $warehouse->name }}</h5>
                            @if($warehouse->is_default)
                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-primary">{{ __('warehouses.default') }}</span>
                            @endif
                        </div>
                        <div class="flex-auto p-2">
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('warehouses.code') }}</span><span>{{ $warehouse->code }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('warehouses.city') }}</span><span>{{ $warehouse->city ?: '—' }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('warehouses.locations') }}</span><span>{{ $warehouse->locations_count }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('warehouses.status') }}</span>
                                    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-{{ $warehouse->is_active ? 'success' : 'secondary' }}">{{ $warehouse->is_active ? __('warehouses.active') : __('warehouses.inactive') }}</span>
                                </li>
                            </ul>
                            <div class="btn-group">
                                @can('edit_warehouses')
                                    <a href="{{ route('warehouses.edit', $warehouse) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>
                                @endcan
                                @can('delete_warehouses')
                                    <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $warehouse->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('warehouses.no_warehouses_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">{{ $warehouses->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
