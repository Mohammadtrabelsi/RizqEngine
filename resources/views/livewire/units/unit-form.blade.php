<form wire:submit="save">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="name">{{ __('units.name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                                @error('name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="short_name">{{ __('units.short_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('short_name') is-invalid @enderror" wire:model="short_name">
                                @error('short_name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="operator">{{ __('units.operator') }}</label>
                                <input type="text" class="form-control" wire:model="operator" placeholder="ex: * / + -">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="operation_value">{{ __('units.operation_value') }}</label>
                                <input type="text" class="form-control" wire:model="operation_value" placeholder="Enter a number">
                            </div>
                        </div>
                        <div class="col-lg-12 d-flex justify-content-end">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    {{ $unitId ? __('units.update_unit') : __('units.create_unit') }} <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
