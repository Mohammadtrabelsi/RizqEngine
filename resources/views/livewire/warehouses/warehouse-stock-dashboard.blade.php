{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        {{-- Filters --}}
        <div class="row mb-3">
            <div class="col-12 col-md-4 mb-2">
                <select wire:model.live="warehouseId" class="form-control">
                    <option value="0">{{ __('warehouses.all_warehouses') }}</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}{{ $warehouse->is_default ? ' ('.__('warehouses.default').')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-5 mb-2">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
            </div>
            <div class="col-12 col-md-3 mb-2 text-md-end">
                @can('access_stock_exits')
                    <a href="{{ route('stock-exits.index') }}" class="btn btn-outline">
                        <i class="bi bi-box-arrow-up"></i> {{ __('stockexit.stock_exits') }}
                    </a>
                @endcan
            </div>
        </div>

        {{-- Per-warehouse on-hand totals --}}
        <div class="row">
            @foreach($warehouses as $warehouse)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-3" wire:key="wtotal-{{ $warehouse->id }}">
                    <div class="relative flex flex-col min-w-0 break-words border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm h-100">
                        <div class="flex-auto p-2">
                            <h6 class="text-muted mb-1 d-flex justify-content-between align-items-center">
                                <span>{{ $warehouse->name }}</span>
                                @if($warehouse->is_default)
                                    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-primary">{{ __('warehouses.default') }}</span>
                                @endif
                            </h6>
                            <div class="h3 mb-0">{{ number_format((int) ($totalsByWarehouse[$warehouse->id] ?? 0)) }}</div>
                            <small class="text-muted">{{ __('warehouses.units_on_hand') }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Detailed stock table --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl bg-white"><strong>{{ __('warehouses.stock_state') }}</strong></div>
            <div class="flex-auto p-2 p-0">
                <div class="block w-full overflow-x-auto">
                    <table class="w-full mb-4 text-slate-900 border-collapse [&_tbody_tr:hover]:bg-slate-50 mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('warehouses.warehouse') }}</th>
                                <th>{{ __('warehouses.product') }}</th>
                                <th>{{ __('warehouses.code') }}</th>
                                <th class="text-end">{{ __('warehouses.quantity') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                <tr wire:key="row-{{ $row->warehouse_id }}-{{ $row->product_id }}">
                                    <td>{{ $row->warehouse_name }}</td>
                                    <td>{{ $row->product_name }}</td>
                                    <td>{{ $row->product_code }}</td>
                                    <td class="text-end">
                                        <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-{{ $row->quantity <= $row->product_stock_alert ? 'warning' : 'success' }}">
                                            {{ number_format((int) $row->quantity) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">{{ __('warehouses.no_stock') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Outstanding Bons de Sortie --}}
        <div class="card border-0 shadow-sm">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl bg-white"><strong>{{ __('warehouses.outstanding_exits') }}</strong></div>
            <div class="flex-auto p-2 p-0">
                <div class="block w-full overflow-x-auto">
                    <table class="w-full mb-4 text-slate-900 border-collapse [&_tbody_tr:hover]:bg-slate-50 mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('stockexit.reference') }}</th>
                                <th>{{ __('stockexit.kind') }}</th>
                                <th>{{ __('warehouses.customer') }}</th>
                                <th class="text-end">{{ __('warehouses.outstanding_qty') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($outstandingExits as $exit)
                                <tr wire:key="exit-{{ $exit->id }}">
                                    <td>{{ $exit->reference }}</td>
                                    <td>{{ __('stockexit.kind_'.$exit->kind) }}</td>
                                    <td>{{ $exit->customer->customer_name ?? '—' }}</td>
                                    <td class="text-end">{{ number_format((int) $exit->outstanding_quantity) }}</td>
                                    <td class="text-end">
                                        @can('access_stock_exits')
                                            <a href="{{ route('stock-exits.show', $exit) }}" class="btn btn-sm btn-secondary"><i class="bi bi-eye"></i></a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">{{ __('warehouses.no_outstanding_exits') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
