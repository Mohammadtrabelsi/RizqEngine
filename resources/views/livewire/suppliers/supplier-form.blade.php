{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
<div class="container-fluid">
    <form wire:submit="save">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    {{ $supplierId ? __('supplier.update_supplier') : __('supplier.create_supplier') }} <i class="bi bi-check"></i>
                </button>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('supplier.supplier_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('supplier_name') is-invalid @enderror" wire:model="supplier_name">
                                @error('supplier_name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('supplier.supplier_email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('supplier_email') is-invalid @enderror" wire:model="supplier_email">
                                @error('supplier_email') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('supplier.supplier_phone') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('supplier_phone') is-invalid @enderror" wire:model="supplier_phone">
                                @error('supplier_phone') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('supplier.tax_identification_number') }}</label>
                                <input type="text" class="form-control @error('tax_identification_number') is-invalid @enderror" wire:model="tax_identification_number">
                                @error('tax_identification_number') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('supplier.city') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" wire:model="city">
                                @error('city') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('supplier.country') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror" wire:model="country">
                                @error('country') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('supplier.address') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" wire:model="address">
                                @error('address') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('supplier.profile_image') }}</label>
                                @if ($supplier && $supplier->getFirstMediaUrl('images'))
                                    <img src="{{ $supplier->getFirstMediaUrl('images') }}" class="img-max-120 img-fluid img-thumbnail mb-2" alt="Supplier Image">
                                @endif
                                <input type="file" class="form-control-file @error('image') is-invalid @enderror" wire:model="image">
                                @error('image') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
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
