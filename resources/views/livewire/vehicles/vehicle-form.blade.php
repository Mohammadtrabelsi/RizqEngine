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
                                        <label for="registration">{{ __('vehicles.registration') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('registration') is-invalid @enderror" wire:model="registration">
                                        @error('registration') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="brand">{{ __('vehicles.brand') }}</label>
                                        <input type="text" class="form-control @error('brand') is-invalid @enderror" wire:model="brand">
                                        @error('brand') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="model">{{ __('vehicles.model') }}</label>
                                        <input type="text" class="form-control @error('model') is-invalid @enderror" wire:model="model">
                                        @error('model') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="note">{{ __('vehicles.note') }}</label>
                                        <textarea class="form-control @error('note') is-invalid @enderror" rows="3" wire:model="note"></textarea>
                                        @error('note') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12 d-flex justify-content-end">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            {{ $vehicleId ? __('vehicles.update_vehicle') : __('vehicles.create_vehicle') }} <i class="bi bi-check"></i>
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
