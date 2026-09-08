{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="card border-0 shadow-sm mb-3">
            <div class="flex-auto p-2">
                <form wire:submit="register" class="form-row align-items-end">
                    <div class="col-lg-5">
                        <div class="mb-4 mb-0">
                            <label>{{ __('batches.product') }}</label>
                            <select class="form-control @error('product_id') !border-red-500 @enderror" wire:model="product_id">
                                <option value="">{{ __('batches.select_product') }}</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->product_name }} — {{ $product->product_code }}</option>
                                @endforeach
                            </select>
                            @error('product_id') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="mb-4 mb-0">
                            <label>{{ __('batches.serial') }}</label>
                            <input type="text" class="form-control @error('serial') !border-red-500 @enderror" wire:model="serial" placeholder="{{ __('batches.scan_or_type_serial') }}">
                            @error('serial') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-primary w-100">{{ __('batches.register_serial') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
            </div>
            <div class="col-12 col-md-6 mb-3">
                <select class="form-control" wire:model.live="statusFilter">
                    <option value="">{{ __('batches.all_statuses') }}</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="flex-auto p-2 block w-full overflow-x-auto">
                <table class="w-full mb-4 text-slate-900 border-collapse [&_tbody_tr:hover]:bg-slate-50 align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('batches.serial') }}</th>
                            <th>{{ __('batches.product') }}</th>
                            <th>{{ __('batches.status') }}</th>
                            <th class="text-end">{{ __('batches.change_status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serials as $serial)
                            <tr wire:key="serial-{{ $serial->id }}">
                                <td>{{ $serial->serial }}</td>
                                <td>{{ $serial->product?->product_name }} <small class="text-muted">{{ $serial->product?->product_code }}</small></td>
                                <td><span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-{{ $serial->status->color() }}">{{ $serial->status->label() }}</span></td>
                                <td class="text-end">
                                    <select class="form-control form-control-sm d-inline-block" style="width:auto"
                                            wire:change="changeStatus({{ $serial->id }}, $event.target.value)">
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->value }}" @selected($serial->status === $status)>{{ $status->label() }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">{{ __('batches.no_serials_found') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-3">{{ $serials->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
