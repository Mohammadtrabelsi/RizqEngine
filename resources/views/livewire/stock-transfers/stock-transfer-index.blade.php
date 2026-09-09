{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                @can('create_stock_transfers')
                    <a href="{{ route('stock-transfers.create') }}" class="btn btn-primary">
                        {{ __('warehouses.add_transfer') }} <i class="bi bi-plus"></i>
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
                    <div class="col-12 col-lg-3 mb-3">
                        <label class="form-label small text-muted mb-1">{{ __('warehouses.status') }}</label>
                        <select wire:model.live="status" class="form-select">
                            <option value="">{{ __('app.all') }}</option>
                            @foreach(\App\Enums\TransferStatus::cases() as $transferStatus)
                                <option value="{{ $transferStatus->value }}">{{ $transferStatus->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-3 mb-3">
                        <label class="form-label small text-muted mb-1">{{ __('warehouses.warehouse') }}</label>
                        <select wire:model.live="warehouseId" class="form-select">
                            <option value="">{{ __('app.all') }}</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-2 mb-3">
                        <label class="form-label small text-muted mb-1">{{ __('reports.start_date') }}</label>
                        <input type="date" wire:model.live="dateFrom" class="form-control">
                    </div>
                    <div class="col-12 col-lg-2 mb-3">
                        <label class="form-label small text-muted mb-1">{{ __('reports.end_date') }}</label>
                        <input type="date" wire:model.live="dateTo" class="form-control">
                    </div>
                    <div class="col-12 col-lg-2 mb-3">
                        <button type="button" wire:click="resetFilters" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> {{ __('app.reset') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="flex-auto p-2 block w-full overflow-x-auto">
                <table class="w-full mb-4 text-slate-900 border-collapse [&_tbody_tr:hover]:bg-slate-50 align-middle">
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
