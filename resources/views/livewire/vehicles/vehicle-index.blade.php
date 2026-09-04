{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                @can('create_vehicles')
                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                        {{ __('vehicles.add_vehicle') }} <i class="bi bi-plus"></i>
                    </a>
                @endcan
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
            </div>
        </div>

        <div class="d-flex justify-content-center mb-3">{{ $vehicles->links('pagination::bootstrap-5') }}</div>
        <div class="row">
            @forelse($vehicles as $vehicle)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="vehicle-{{ $vehicle->id }}">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $vehicle->registration }}</h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('vehicles.brand') }}</span><span>{{ $vehicle->brand ?: '—' }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('vehicles.model') }}</span><span>{{ $vehicle->model ?: '—' }}</span></li>
                            </ul>
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
                    <div class="card"><div class="card-body text-center text-muted">{{ __('vehicles.no_vehicles_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $vehicles->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
