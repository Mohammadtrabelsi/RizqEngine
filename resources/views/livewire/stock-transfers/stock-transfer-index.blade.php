{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                @can('create_stock_transfers')
                    <a href="{{ route('stock-transfers.create') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                        {{ __('warehouses.add_transfer') }} <i class="bi bi-plus"></i>
                    </a>
                @endcan
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="{{ __('app.search') }}">
            </div>
        </div>

        <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
            <div class="flex-auto p-5 table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('warehouses.reference') }}</th>
                            <th>{{ __('warehouses.date') }}</th>
                            <th>{{ __('warehouses.from') }}</th>
                            <th>{{ __('warehouses.to') }}</th>
                            <th class="text-end">{{ __('warehouses.lines') }}</th>
                            <th>{{ __('warehouses.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $transfer)
                            <tr wire:key="transfer-{{ $transfer->id }}">
                                <td>{{ $transfer->reference }}</td>
                                <td>{{ $transfer->date?->format('Y-m-d') }}</td>
                                <td>{{ $transfer->fromWarehouse?->name }}</td>
                                <td>{{ $transfer->toWarehouse?->name }}</td>
                                <td class="text-end">{{ $transfer->lines->count() }}</td>
                                <td><span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-{{ $transfer->status->color() }}">{{ $transfer->status->label() }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">{{ __('warehouses.no_transfers_found') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-3">{{ $transfers->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
