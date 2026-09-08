{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <form wire:submit="save">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="flex-auto p-2">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="name">{{ __('taxes.name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') !border-red-500 @enderror" wire:model="name">
                                        @error('name') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="type">{{ __('taxes.type') }} <span class="text-danger">*</span></label>
                                        <select class="form-control @error('type') !border-red-500 @enderror" wire:model="type">
                                            <option value="percentage">{{ __('taxes.type_percentage') }}</option>
                                            <option value="fixed">{{ __('taxes.type_fixed') }}</option>
                                        </select>
                                        @error('type') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="rate">{{ $type === 'fixed' ? __('taxes.amount') : __('taxes.rate').' (%)' }} <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" @if($type !== 'fixed') max="100" @endif class="form-control @error('rate') !border-red-500 @enderror" wire:model="rate">
                                        @error('rate') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="apply_to">{{ __('taxes.apply_to') }} <span class="text-danger">*</span></label>
                                        <select class="form-control @error('apply_to') !border-red-500 @enderror" wire:model="apply_to">
                                            <option value="product">{{ __('taxes.apply_to_product') }}</option>
                                            <option value="purchase">{{ __('taxes.apply_to_purchase') }}</option>
                                            <option value="sale">{{ __('taxes.apply_to_sale') }}</option>
                                            <option value="order">{{ __('taxes.apply_to_order') }}</option>
                                        </select>
                                        @error('apply_to') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="order">{{ __('taxes.order') }} <span class="text-danger">*</span></label>
                                        <input type="number" step="1" min="0" class="form-control @error('order') !border-red-500 @enderror" wire:model="order">
                                        <small class="block mt-1 text-xs text-slate-500 text-muted">{{ __('taxes.order_help') }}</small>
                                        @error('order') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12 d-flex justify-content-end">
                                    <div class="mb-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ $taxId ? __('taxes.update_tax') : __('taxes.create_tax') }} <i class="bi bi-check"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
