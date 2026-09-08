{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
<div class="container-fluid">
    <form wire:submit="save">
    <div class="row">
        <div class="col-lg-12">
            <div class="mb-4">
                <button type="submit" class="btn btn-primary">
                    {{ $supplierId ? __('supplier.update_supplier') : __('supplier.create_supplier') }} <i class="bi bi-check"></i>
                </button>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="flex-auto p-2">
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.supplier_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('supplier_name') !border-red-500 @enderror" wire:model="supplier_name">
                                @error('supplier_name') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.supplier_email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('supplier_email') !border-red-500 @enderror" wire:model="supplier_email">
                                @error('supplier_email') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.supplier_phone') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('supplier_phone') !border-red-500 @enderror" wire:model="supplier_phone">
                                @error('supplier_phone') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.whatsapp_number') }}</label>
                                <input type="text" class="form-control @error('whatsapp_number') !border-red-500 @enderror" wire:model="whatsapp_number">
                                @error('whatsapp_number') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.responsible_person') }}</label>
                                <input type="text" class="form-control @error('responsible_person') !border-red-500 @enderror" wire:model="responsible_person">
                                @error('responsible_person') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.tax_identification_number') }}</label>
                                <input type="text" class="form-control @error('tax_identification_number') !border-red-500 @enderror" wire:model="tax_identification_number">
                                @error('tax_identification_number') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('withholding.legal_form') }}</label>
                                <input type="text" class="form-control @error('legal_form') !border-red-500 @enderror" wire:model="legal_form">
                                @error('legal_form') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('withholding.fiscal_category') }}</label>
                                <input type="text" class="form-control @error('fiscal_category') !border-red-500 @enderror" wire:model="fiscal_category">
                                @error('fiscal_category') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-check mb-4 mt-4">
                                <input class="form-check-input" type="checkbox" wire:model="subject_to_withholding" id="subject_to_withholding">
                                <label class="form-check-label" for="subject_to_withholding">{{ __('withholding.subject_to_withholding') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.iban') }}</label>
                                <input type="text" class="form-control @error('iban') !border-red-500 @enderror" wire:model="iban">
                                @error('iban') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.city') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('city') !border-red-500 @enderror" wire:model="city">
                                @error('city') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.country') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('country') !border-red-500 @enderror" wire:model="country">
                                @error('country') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-12">
                            <div class="mb-4">
                                <label>{{ __('supplier.address') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('address') !border-red-500 @enderror" wire:model="address">
                                @error('address') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-12">
                            <div class="mb-4">
                                <label>{{ __('supplier.note') }}</label>
                                <textarea class="form-control @error('note') !border-red-500 @enderror" rows="3" wire:model="note"></textarea>
                                @error('note') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.document') }}</label>
                                @if ($supplier && $supplier->getFirstMediaUrl('documents'))
                                    <a href="{{ $supplier->getFirstMediaUrl('documents') }}" target="_blank" class="d-block mb-2">{{ $supplier->getFirstMedia('documents')->file_name }}</a>
                                @endif
                                <input type="file" class="block w-full @error('document') !border-red-500 @enderror" wire:model="document">
                                <small class="block mt-1 text-xs text-slate-500 text-muted">{{ __('supplier.document_hint') }}</small>
                                @error('document') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.profile_image') }}</label>
                                @if ($supplier && $supplier->getFirstMediaUrl('images'))
                                    <img src="{{ $supplier->getFirstMediaUrl('images') }}" class="img-max-120 img-fluid img-thumbnail mb-2" alt="Supplier Image">
                                @endif
                                <input type="file" class="block w-full @error('image') !border-red-500 @enderror" wire:model="image">
                                @error('image') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
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
