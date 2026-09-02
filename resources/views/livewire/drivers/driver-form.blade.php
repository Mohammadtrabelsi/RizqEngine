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
                                        <label for="name">{{ __('drivers.name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                                        @error('name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="phone">{{ __('drivers.phone') }}</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model="phone">
                                        @error('phone') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="license_number">{{ __('drivers.license_number') }}</label>
                                        <input type="text" class="form-control @error('license_number') is-invalid @enderror" wire:model="license_number">
                                        @error('license_number') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="note">{{ __('drivers.note') }}</label>
                                        <textarea class="form-control @error('note') is-invalid @enderror" rows="3" wire:model="note"></textarea>
                                        @error('note') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12 d-flex justify-content-end">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            {{ $driverId ? __('drivers.update_driver') : __('drivers.create_driver') }} <i class="bi bi-check"></i>
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
