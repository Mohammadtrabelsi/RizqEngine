{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <form wire:submit="save">
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                            <h5 class="mb-0"><i class="bi bi-building text-primary"></i> {{ $warehouseId ? __('warehouses.update_warehouse') : __('warehouses.create_warehouse') }}</h5>
                        </div>
                        <div class="flex-auto p-2">
                            <div class="form-row">
                                <div class="col-12 col-lg-6">
                                    <div class="mb-4">
                                        <label>{{ __('warehouses.name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') !border-red-500 @enderror" wire:model="name">
                                        @error('name') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="mb-4">
                                        <label>{{ __('warehouses.code') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('code') !border-red-500 @enderror" wire:model="code">
                                        @error('code') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="mb-4">
                                        <label>{{ __('warehouses.phone') }}</label>
                                        <input type="text" class="form-control @error('phone') !border-red-500 @enderror" wire:model="phone">
                                        @error('phone') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="mb-4">
                                        <label>{{ __('warehouses.city') }}</label>
                                        <input type="text" class="form-control @error('city') !border-red-500 @enderror" wire:model="city">
                                        @error('city') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-4">
                                        <label>{{ __('warehouses.address') }}</label>
                                        <textarea class="form-control @error('address') !border-red-500 @enderror" rows="2" wire:model="address"></textarea>
                                        @error('address') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-check mt-2">
                                        <input type="checkbox" class="form-check-input" id="is_default" wire:model="is_default">
                                        <label class="form-check-label" for="is_default">{{ __('warehouses.set_as_default') }}</label>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-check mt-2">
                                        <input type="checkbox" class="form-check-input" id="is_active" wire:model="is_active">
                                        <label class="form-check-label" for="is_active">{{ __('warehouses.active') }}</label>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="mb-4 mb-0">
                                        <label>{{ __('warehouses.note') }}</label>
                                        <textarea class="form-control @error('note') !border-red-500 @enderror" rows="3" wire:model="note"></textarea>
                                        @error('note') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 text-end">
                            <button type="submit" class="btn btn-primary">
                                {{ $warehouseId ? __('warehouses.update_warehouse') : __('warehouses.create_warehouse') }} <i class="bi bi-check"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
