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
                                        <label>{{ __('warehouses.name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                                        @error('name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>{{ __('warehouses.code') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('code') is-invalid @enderror" wire:model="code">
                                        @error('code') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>{{ __('warehouses.phone') }}</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model="phone">
                                        @error('phone') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>{{ __('warehouses.city') }}</label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror" wire:model="city">
                                        @error('city') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>{{ __('warehouses.address') }}</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" rows="2" wire:model="address"></textarea>
                                        @error('address') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-check mt-2">
                                        <input type="checkbox" class="form-check-input" id="is_default" wire:model="is_default">
                                        <label class="form-check-label" for="is_default">{{ __('warehouses.set_as_default') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-check mt-2">
                                        <input type="checkbox" class="form-check-input" id="is_active" wire:model="is_active">
                                        <label class="form-check-label" for="is_active">{{ __('warehouses.active') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-3">
                                    <div class="form-group">
                                        <label>{{ __('warehouses.note') }}</label>
                                        <textarea class="form-control @error('note') is-invalid @enderror" rows="3" wire:model="note"></textarea>
                                        @error('note') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        {{ $warehouseId ? __('warehouses.update_warehouse') : __('warehouses.create_warehouse') }} <i class="bi bi-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
