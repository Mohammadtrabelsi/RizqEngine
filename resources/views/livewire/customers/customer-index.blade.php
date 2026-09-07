{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            @can('create_customers')
                <a href="{{ route('customers.create') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                    {{ __('customer.add_customer') }} <i class="bi bi-plus"></i>
                </a>
                <a href="{{ route('customers.import') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-indigo-600 border-indigo-600 hover:bg-indigo-600 hover:!text-white">
                    {{ __('nav.import_customers') }} <i class="bi bi-upload"></i>
                </a>
            @endcan
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    {{-- Filters container --}}
    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm mb-4">
        <div class="flex-auto p-5">
            <h6 class="mb-3 text-lg font-semibold text-slate-900 text-muted mb-3">
                <i class="bi bi-funnel"></i> {{ __('app.filters') }}
            </h6>
            <div class="row align-items-end">
                <div class="col-12 col-md-3 mb-3">
                    <label class="inline-block mb-1.5 text-sm font-medium text-slate-900 small text-muted mb-1">{{ __('customer.city') }}</label>
                    <select wire:model.live="city" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 pr-9 text-sm leading-normal text-slate-900 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                        <option value="">{{ __('app.all') }}</option>
                        @foreach($cities as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <label class="inline-block mb-1.5 text-sm font-medium text-slate-900 small text-muted mb-1">{{ __('customer.country') }}</label>
                    <select wire:model.live="country" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 pr-9 text-sm leading-normal text-slate-900 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                        <option value="">{{ __('app.all') }}</option>
                        @foreach($countries as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <label class="inline-block mb-1.5 text-sm font-medium text-slate-900 small text-muted mb-1">{{ __('customer.tax_identification_number') }}</label>
                    <select wire:model.live="hasTaxId" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 pr-9 text-sm leading-normal text-slate-900 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="yes">{{ __('app.yes') }}</option>
                        <option value="no">{{ __('app.no') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <button type="button" wire:click="resetFilters" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-slate-600 border-slate-300 hover:bg-slate-50 hover:!text-slate-900 hover:border-slate-400 w-100">
                        <i class="bi bi-x-circle"></i> {{ __('app.reset') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $customers->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($customers as $customer)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="customer-{{ $customer->id }}">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 h-100">
                    <div class="position-relative overflow-hidden rounded-top media-thumb">
                        @if ($customer->image_url)
                            <img src="{{ $customer->image_url }}" class="w-100 h-100 thumb-cover" alt="{{ $customer->customer_name }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center w-100 h-100 text-muted">
                                <i class="bi bi-person-circle thumb-placeholder-icon"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-auto p-5">
                        <h5 class="mb-3 text-lg font-semibold text-slate-900">{{ $customer->customer_name }}</h5>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item px-0"><i class="bi bi-envelope"></i> {{ $customer->customer_email }}</li>
                            <li class="list-group-item px-0"><i class="bi bi-telephone"></i> {{ $customer->customer_phone }}</li>
                            @if ($customer->tax_identification_number)
                                <li class="list-group-item px-0"><i class="bi bi-receipt"></i> {{ $customer->tax_identification_number }}</li>
                            @endif
                        </ul>
                        <div class="btn-group">
                            @can('edit_customers')
                                <a href="{{ route('customers.edit', $customer->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-indigo-600 border-indigo-600 hover:bg-indigo-600 hover:!text-white !px-3 !py-1.5 !text-xs"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('show_customers')
                                <a href="{{ route('customers.show', $customer->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-indigo-600 border-indigo-600 hover:bg-indigo-600 hover:!text-white !px-3 !py-1.5 !text-xs"><i class="bi bi-eye"></i></a>
                            @endcan
                            @can('delete_customers')
                                <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-red-500 border-red-500 hover:bg-red-500 hover:!text-white !px-3 !py-1.5 !text-xs" wire:click="delete({{ $customer->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900"><div class="flex-auto p-5 text-center text-muted">{{ __('customer.no_customers_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $customers->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>
