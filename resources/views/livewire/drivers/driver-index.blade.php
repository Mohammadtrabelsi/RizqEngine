{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                @can('create_drivers')
                    <a href="{{ route('drivers.create') }}" class="btn btn-primary">
                        {{ __('drivers.add_driver') }} <i class="bi bi-plus"></i>
                    </a>
                @endcan
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
            </div>
        </div>

        <div class="d-flex justify-content-center mb-3">{{ $drivers->links('pagination::bootstrap-5') }}</div>
        <div class="row">
            @forelse($drivers as $driver)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="driver-{{ $driver->id }}">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $driver->name }}</h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('drivers.phone') }}</span><span>{{ $driver->phone ?: '—' }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('drivers.license_number') }}</span><span>{{ $driver->license_number ?: '—' }}</span></li>
                            </ul>
                            <div class="btn-group">
                                @can('edit_drivers')
                                    <a href="{{ route('drivers.edit', $driver) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>
                                @endcan
                                @can('delete_drivers')
                                    <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $driver->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">{{ __('drivers.no_drivers_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $drivers->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
