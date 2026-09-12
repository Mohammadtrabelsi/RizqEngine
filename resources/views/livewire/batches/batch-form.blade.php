{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <form wire:submit="save">
            <x-form-card :title="$batchId ? __('batches.update_batch') : __('batches.create_batch')" icon="bi-box-seam">
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('batches.product') }} <span class="text-danger">*</span></label>
                                <select class="form-control @error('product_id') !border-red-500 @enderror" wire:model="product_id">
                                    <option value="">{{ __('batches.select_product') }}</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->product_name }} — {{ $product->product_code }}</option>
                                    @endforeach
                                </select>
                                @error('product_id') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('batches.warehouse') }}</label>
                                <select class="form-control @error('warehouse_id') !border-red-500 @enderror" wire:model="warehouse_id">
                                    <option value="">—</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }} ({{ $warehouse->code }})</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('batches.batch_number') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('batch_number') !border-red-500 @enderror" wire:model="batch_number">
                                @error('batch_number') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('batches.quantity') }} <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control @error('quantity') !border-red-500 @enderror" wire:model="quantity">
                                @error('quantity') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('batches.manufactured_date') }}</label>
                                <input type="date" class="form-control @error('manufactured_date') !border-red-500 @enderror" wire:model="manufactured_date">
                                @error('manufactured_date') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('batches.expiry_date') }}</label>
                                <input type="date" class="form-control @error('expiry_date') !border-red-500 @enderror" wire:model="expiry_date">
                                @error('expiry_date') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-4">
                                <label>{{ __('batches.note') }}</label>
                                <textarea class="form-control" rows="3" wire:model="note"></textarea>
                            </div>
                        </div>
                    </div>

                    <x-slot:footer>
                        <button type="submit" class="btn btn-primary">
                            {{ $batchId ? __('batches.update_batch') : __('batches.create_batch') }} <i class="bi bi-check"></i>
                        </button>
                    </x-slot:footer>
            </x-form-card>
        </form>
    </div>
</div>
