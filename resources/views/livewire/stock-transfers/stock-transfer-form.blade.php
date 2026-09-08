{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <form wire:submit="save">
            <div class="card mb-3">
                <div class="flex-auto p-2">
                    <div class="form-row">
                        <div class="col-lg-4">
                            <div class="mb-4">
                                <label>{{ __('warehouses.from') }} <span class="text-danger">*</span></label>
                                <select class="form-control @error('from_warehouse_id') !border-red-500 @enderror" wire:model="from_warehouse_id">
                                    <option value="">{{ __('warehouses.select_warehouse') }}</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }} ({{ $warehouse->code }})</option>
                                    @endforeach
                                </select>
                                @error('from_warehouse_id') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-4">
                                <label>{{ __('warehouses.to') }} <span class="text-danger">*</span></label>
                                <select class="form-control @error('to_warehouse_id') !border-red-500 @enderror" wire:model="to_warehouse_id">
                                    <option value="">{{ __('warehouses.select_warehouse') }}</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }} ({{ $warehouse->code }})</option>
                                    @endforeach
                                </select>
                                @error('to_warehouse_id') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-4">
                                <label>{{ __('warehouses.date') }} <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date') !border-red-500 @enderror" wire:model="date">
                                @error('date') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="flex-auto p-2">
                    @error('lines') <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-red-50 text-red-700 border-red-200">{{ $message }}</div> @enderror
                    <table class="w-full mb-4 text-slate-900 border-collapse align-middle">
                        <thead>
                            <tr>
                                <th style="width:65%">{{ __('warehouses.product') }}</th>
                                <th style="width:25%">{{ __('warehouses.quantity') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lines as $index => $line)
                                <tr wire:key="line-{{ $index }}">
                                    <td>
                                        <select class="form-control @error('lines.'.$index.'.product_id') !border-red-500 @enderror" wire:model="lines.{{ $index }}.product_id">
                                            <option value="">{{ __('warehouses.select_product') }}</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->product_name }} — {{ $product->product_code }}</option>
                                            @endforeach
                                        </select>
                                        @error('lines.'.$index.'.product_id') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </td>
                                    <td>
                                        <input type="number" min="1" class="form-control @error('lines.'.$index.'.quantity') !border-red-500 @enderror" wire:model="lines.{{ $index }}.quantity">
                                        @error('lines.'.$index.'.quantity') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm" wire:click="removeLine({{ $index }})"><i class="bi bi-x"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-outline btn-sm" wire:click="addLine">
                        {{ __('warehouses.add_line') }} <i class="bi bi-plus"></i>
                    </button>
                </div>
            </div>

            <div class="card mb-3">
                <div class="flex-auto p-2">
                    <div class="mb-4">
                        <label>{{ __('warehouses.note') }}</label>
                        <textarea class="form-control" rows="2" wire:model="note"></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            {{ __('warehouses.create_transfer') }} <i class="bi bi-check"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
