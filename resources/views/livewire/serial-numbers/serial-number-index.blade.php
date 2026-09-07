{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm mb-3">
            <div class="flex-auto p-5">
                <form wire:submit="register" class="form-row align-items-end">
                    <div class="col-lg-5">
                        <div class="mb-4 mb-0">
                            <label>{{ __('batches.product') }}</label>
                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('product_id') !border-red-500 @enderror" wire:model="product_id">
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
                            <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('serial') !border-red-500 @enderror" wire:model="serial" placeholder="{{ __('batches.scan_or_type_serial') }}">
                            @error('serial') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700 w-100">{{ __('batches.register_serial') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="{{ __('app.search') }}">
            </div>
            <div class="col-12 col-md-6 mb-3">
                <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" wire:model.live="statusFilter">
                    <option value="">{{ __('batches.all_statuses') }}</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
            <div class="flex-auto p-5 table-responsive">
                <table class="table table-hover align-middle">
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
                                    <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 form-control-sm d-inline-block" style="width:auto"
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
