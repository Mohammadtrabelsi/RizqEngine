{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <form wire:submit="save">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="name">{{ __('taxes.name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                                        @error('name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="type">{{ __('taxes.type') }} <span class="text-danger">*</span></label>
                                        <select class="form-control @error('type') is-invalid @enderror" wire:model="type">
                                            <option value="percentage">{{ __('taxes.type_percentage') }}</option>
                                            <option value="fixed">{{ __('taxes.type_fixed') }}</option>
                                        </select>
                                        @error('type') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="rate">{{ $type === 'fixed' ? __('taxes.amount') : __('taxes.rate').' (%)' }} <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" @if($type !== 'fixed') max="100" @endif class="form-control @error('rate') is-invalid @enderror" wire:model="rate">
                                        @error('rate') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="apply_to">{{ __('taxes.apply_to') }} <span class="text-danger">*</span></label>
                                        <select class="form-control @error('apply_to') is-invalid @enderror" wire:model="apply_to">
                                            <option value="order">{{ __('taxes.apply_to_order') }}</option>
                                            <option value="product">{{ __('taxes.apply_to_product') }}</option>
                                        </select>
                                        @error('apply_to') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="order">{{ __('taxes.order') }} <span class="text-danger">*</span></label>
                                        <input type="number" step="1" min="0" class="form-control @error('order') is-invalid @enderror" wire:model="order">
                                        <small class="form-text text-muted">{{ __('taxes.order_help') }}</small>
                                        @error('order') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12 d-flex justify-content-end">
                                    <div class="form-group">
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
