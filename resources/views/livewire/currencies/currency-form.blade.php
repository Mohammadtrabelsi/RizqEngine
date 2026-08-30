{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <form wire:submit="save">
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            {{ $currencyId ? __('currency.update_currency') : __('currency.create_currency') }} <i class="bi bi-check"></i>
                        </button>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="currency_name">{{ __('currency.currency_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('currency_name') is-invalid @enderror" wire:model="currency_name">
                                        @error('currency_name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="code">{{ __('currency.currency_code') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('code') is-invalid @enderror" wire:model="code">
                                        @error('code') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="symbol">{{ __('currency.symbol') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('symbol') is-invalid @enderror" wire:model="symbol">
                                        @error('symbol') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="thousand_separator">{{ __('currency.thousand_separator') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('thousand_separator') is-invalid @enderror" wire:model="thousand_separator">
                                        @error('thousand_separator') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="decimal_separator">{{ __('currency.decimal_separator') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('decimal_separator') is-invalid @enderror" wire:model="decimal_separator">
                                        @error('decimal_separator') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
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
