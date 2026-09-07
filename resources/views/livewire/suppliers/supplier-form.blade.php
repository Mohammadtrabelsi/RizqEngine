{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
<div class="container-fluid">
    <form wire:submit="save">
    <div class="row">
        <div class="col-lg-12">
            <div class="mb-4">
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                    {{ $supplierId ? __('supplier.update_supplier') : __('supplier.create_supplier') }} <i class="bi bi-check"></i>
                </button>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
                <div class="flex-auto p-5">
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.supplier_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('supplier_name') !border-red-500 @enderror" wire:model="supplier_name">
                                @error('supplier_name') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.supplier_email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('supplier_email') !border-red-500 @enderror" wire:model="supplier_email">
                                @error('supplier_email') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.supplier_phone') }} <span class="text-danger">*</span></label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('supplier_phone') !border-red-500 @enderror" wire:model="supplier_phone">
                                @error('supplier_phone') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.whatsapp_number') }}</label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('whatsapp_number') !border-red-500 @enderror" wire:model="whatsapp_number">
                                @error('whatsapp_number') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.responsible_person') }}</label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('responsible_person') !border-red-500 @enderror" wire:model="responsible_person">
                                @error('responsible_person') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.tax_identification_number') }}</label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('tax_identification_number') !border-red-500 @enderror" wire:model="tax_identification_number">
                                @error('tax_identification_number') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.iban') }}</label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('iban') !border-red-500 @enderror" wire:model="iban">
                                @error('iban') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.city') }} <span class="text-danger">*</span></label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('city') !border-red-500 @enderror" wire:model="city">
                                @error('city') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('supplier.country') }} <span class="text-danger">*</span></label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('country') !border-red-500 @enderror" wire:model="country">
                                @error('country') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-12">
                            <div class="mb-4">
                                <label>{{ __('supplier.address') }} <span class="text-danger">*</span></label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('address') !border-red-500 @enderror" wire:model="address">
                                @error('address') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-12">
                            <div class="mb-4">
                                <label>{{ __('supplier.note') }}</label>
                                <textarea class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('note') !border-red-500 @enderror" rows="3" wire:model="note"></textarea>
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
