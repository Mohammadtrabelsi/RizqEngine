<form wire:submit="save">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    {{ $customerId ? __('customer.update') : __('customer.create') }} <i class="bi bi-check"></i>
                </button>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('customer.customer_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" wire:model="customer_name">
                                @error('customer_name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('customer.customer_email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('customer_email') is-invalid @enderror" wire:model="customer_email">
                                @error('customer_email') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>{{ __('customer.customer_phone') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" wire:model="customer_phone">
                                @error('customer_phone') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>{{ __('customer.city') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" wire:model="city">
                                @error('city') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>{{ __('customer.country') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror" wire:model="country">
                                @error('country') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('customer.address') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" wire:model="address">
                                @error('address') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('customer.profile_image') }}</label>
                                @if ($customer && $customer->getFirstMediaUrl('images'))
                                    <img src="{{ $customer->getFirstMediaUrl('images') }}" class="img-fluid img-thumbnail mb-2" alt="Customer Image" style="max-width: 120px;">
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
