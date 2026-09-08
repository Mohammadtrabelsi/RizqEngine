{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                @can('create_drivers')
                    <a href="{{ route('drivers.create') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                        {{ __('drivers.add_driver') }} <i class="bi bi-plus"></i>
                    </a>
                @endcan
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="{{ __('app.search') }}">
            </div>
        </div>

        <div class="d-flex justify-content-center mb-3">{{ $drivers->links('pagination::bootstrap-5') }}</div>
        <div class="row">
            @forelse($drivers as $driver)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="driver-{{ $driver->id }}">
                    <div class="relative flex flex-col min-w-0 break-words border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm h-100">
                        <div class="flex-auto p-2">
                            <h5 class="mb-3 text-lg font-semibold text-slate-900">{{ $driver->name }}</h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('drivers.phone') }}</span><span>{{ $driver->phone ?: '—' }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('drivers.license_number') }}</span><span>{{ $driver->license_number ?: '—' }}</span></li>
                            </ul>
                            <div class="btn-group">
                                @can('edit_drivers')
                                    <a href="{{ route('drivers.edit', $driver) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700 !px-3 !py-1.5 !text-xs"><i class="bi bi-pencil"></i></a>
                                @endcan
                                @can('delete_drivers')
                                    <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-red-500 !text-white border-red-500 hover:bg-red-600 hover:border-red-600 !px-3 !py-1.5 !text-xs" wire:click="delete({{ $driver->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900"><div class="flex-auto p-2 text-center text-muted">{{ __('drivers.no_drivers_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $drivers->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
