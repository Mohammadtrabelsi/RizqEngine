{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                @can('create_vehicles')
                    <a href="{{ route('vehicles.create') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                        {{ __('vehicles.add_vehicle') }} <i class="bi bi-plus"></i>
                    </a>
                @endcan
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="{{ __('app.search') }}">
            </div>
        </div>

        <div class="d-flex justify-content-center mb-3">{{ $vehicles->links('pagination::bootstrap-5') }}</div>
        <div class="row">
            @forelse($vehicles as $vehicle)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="vehicle-{{ $vehicle->id }}">
                    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm h-100">
                        <div class="flex-auto p-5">
                            <h5 class="mb-3 text-lg font-semibold text-slate-900">{{ $vehicle->registration }}</h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('vehicles.brand') }}</span><span>{{ $vehicle->brand ?: '—' }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('vehicles.model') }}</span><span>{{ $vehicle->model ?: '—' }}</span></li>
                            </ul>
                            <div class="btn-group">
                                @can('edit_vehicles')
                                    <a href="{{ route('vehicles.edit', $vehicle) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700 !px-3 !py-1.5 !text-xs"><i class="bi bi-pencil"></i></a>
                                @endcan
                                @can('delete_vehicles')
                                    <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-red-500 !text-white border-red-500 hover:bg-red-600 hover:border-red-600 !px-3 !py-1.5 !text-xs" wire:click="delete({{ $vehicle->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900"><div class="flex-auto p-5 text-center text-muted">{{ __('vehicles.no_vehicles_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $vehicles->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
