{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <form wire:submit="save">
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                            <h5 class="mb-0"><i class="bi bi-truck-front text-primary"></i> {{ $vehicleId ? __('vehicles.update_vehicle') : __('vehicles.create_vehicle') }}</h5>
                        </div>
                        <div class="flex-auto p-2">
                            <div class="form-row">
                                <div class="col-12 col-lg-6">
                                    <div class="mb-4">
                                        <label for="registration">{{ __('vehicles.registration') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('registration') !border-red-500 @enderror" wire:model="registration">
                                        @error('registration') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="mb-4">
                                        <label for="brand">{{ __('vehicles.brand') }}</label>
                                        <input type="text" class="form-control @error('brand') !border-red-500 @enderror" wire:model="brand">
                                        @error('brand') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="mb-4">
                                        <label for="model">{{ __('vehicles.model') }}</label>
                                        <input type="text" class="form-control @error('model') !border-red-500 @enderror" wire:model="model">
                                        @error('model') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-4 mb-0">
                                        <label for="note">{{ __('vehicles.note') }}</label>
                                        <textarea class="form-control @error('note') !border-red-500 @enderror" rows="3" wire:model="note"></textarea>
                                        @error('note') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 text-end">
                            <button type="submit" class="btn btn-primary">
                                {{ $vehicleId ? __('vehicles.update_vehicle') : __('vehicles.create_vehicle') }} <i class="bi bi-check"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
