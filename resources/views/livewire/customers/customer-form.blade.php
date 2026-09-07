{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
<div class="container-fluid">
    <form wire:submit="save">
    <div class="row">
        <div class="col-lg-12">
            <div class="mb-4">
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                    {{ $customerId ? __('customer.update') : __('customer.create') }} <i class="bi bi-check"></i>
                </button>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
                <div class="flex-auto p-5">
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('customer.customer_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('customer_name') !border-red-500 @enderror" wire:model="customer_name">
                                @error('customer_name') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('customer.client_type') }} <span class="text-danger">*</span></label>
                                <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('client_type') !border-red-500 @enderror" wire:model.live="client_type">
                                    <option value="physical_person">{{ __('customer.physical_person') }}</option>
                                    <option value="legal_entity">{{ __('customer.legal_entity') }}</option>
                                </select>
                                @error('client_type') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('customer.customer_email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('customer_email') !border-red-500 @enderror" wire:model="customer_email">
                                @error('customer_email') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('customer.customer_phone') }} <span class="text-danger">*</span></label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('customer_phone') !border-red-500 @enderror" wire:model="customer_phone">
                                @error('customer_phone') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('customer.whatsapp_number') }}</label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('whatsapp_number') !border-red-500 @enderror" wire:model="whatsapp_number">
                                @error('whatsapp_number') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('customer.responsible_person') }}</label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('responsible_person') !border-red-500 @enderror" wire:model="responsible_person">
                                @error('responsible_person') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('customer.tax_identification_number') }} @if ($client_type === 'legal_entity')<span class="text-danger">*</span>@endif</label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('tax_identification_number') !border-red-500 @enderror" wire:model="tax_identification_number">
                                @error('tax_identification_number') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('customer.iban') }}</label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('iban') !border-red-500 @enderror" wire:model="iban">
                                @error('iban') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @can('manage_customer_credit')
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('customers.credit_limit') }}</label>
                                <input type="number" step="0.01" min="0" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('credit_limit') !border-red-500 @enderror" wire:model="credit_limit">
                                <small class="text-muted">{{ __('customers.credit_limit_hint') }}</small>
                                @error('credit_limit') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @endcan
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('customer.city') }} <span class="text-danger">*</span></label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('city') !border-red-500 @enderror" wire:model="city">
                                @error('city') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('customer.country') }} <span class="text-danger">*</span></label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('country') !border-red-500 @enderror" wire:model="country">
                                @error('country') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-12">
                            <div class="mb-4">
                                <label>{{ __('customer.address') }} <span class="text-danger">*</span></label>
                                <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('address') !border-red-500 @enderror" wire:model="address">
                                @error('address') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-12">
                            <div class="mb-4">
                                <label>{{ __('customer.note') }}</label>
                                <textarea class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('note') !border-red-500 @enderror" rows="3" wire:model="note"></textarea>
                                @error('note') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('customer.document') }}</label>
                                @if ($customer && $customer->getFirstMediaUrl('documents'))
                                    <a href="{{ $customer->getFirstMediaUrl('documents') }}" target="_blank" class="d-block mb-2">{{ $customer->getFirstMedia('documents')->file_name }}</a>
                                @endif
                                <input type="file" class="block w-full @error('document') !border-red-500 @enderror" wire:model="document">
                                <small class="block mt-1 text-xs text-slate-500 text-muted">{{ __('customer.document_hint') }}</small>
                                @error('document') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label>{{ __('customer.profile_image') }}</label>
                                @if ($customer && $customer->getFirstMediaUrl('images'))
                                    <img src="{{ $customer->getFirstMediaUrl('images') }}" class="img-max-120 img-fluid img-thumbnail mb-2" alt="Customer Image">
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
