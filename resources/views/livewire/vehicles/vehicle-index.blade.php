{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                @can('create_vehicles')
                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                        {{ __('vehicles.add_vehicle') }} <i class="bi bi-plus"></i>
                    </a>
                    <a href="{{ route('vehicles.import') }}" class="btn btn-outline">
                        {{ __('import.vehicles') }} <i class="bi bi-upload"></i>
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
                        <label class="form-label small text-muted mb-1">{{ __('vehicles.brand') }}</label>
                        <select wire:model.live="brand" class="form-select">
                            <option value="">{{ __('app.all') }}</option>
                            @foreach($brands as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-6 mb-3">
                        <button type="button" wire:click="resetFilters" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> {{ __('app.reset') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mb-3">{{ $vehicles->links('pagination::bootstrap-5') }}</div>
        <div class="row">
            @forelse($vehicles as $vehicle)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="vehicle-{{ $vehicle->id }}">
                    <div class="card h-100">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                            <h5 class="mb-0">{{ $vehicle->registration }}</h5>
                        </div>
                        <div class="flex-auto p-2">
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('vehicles.brand') }}</span><span>{{ $vehicle->brand ?: '—' }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('vehicles.model') }}</span><span>{{ $vehicle->model ?: '—' }}</span></li>
                            </ul>
                        </div>
                        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 text-center">
                            <div class="btn-group">
                                @can('edit_vehicles')
                                    <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>
                                @endcan
                                @can('delete_vehicles')
                                    <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $vehicle->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('vehicles.no_vehicles_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $vehicles->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
