{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <form wire:submit="save">
            <x-form-card :title="$unitId ? __('units.update_unit') : __('units.create_unit')" icon="bi-rulers">
                <div class="form-row">
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label for="name">{{ __('units.name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') !border-red-500 @enderror" wire:model="name">
                            @error('name') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label for="short_name">{{ __('units.short_name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('short_name') !border-red-500 @enderror" wire:model="short_name">
                            @error('short_name') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="mb-4">
                            <label for="operator">{{ __('units.operator') }}</label>
                            <input type="text" class="form-control" wire:model="operator" placeholder="ex: * / + -">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="mb-4">
                            <label for="operation_value">{{ __('units.operation_value') }}</label>
                            <input type="text" class="form-control" wire:model="operation_value" placeholder="Enter a number">
                        </div>
                    </div>
                </div>

                <x-slot:footer>
                    <button type="submit" class="btn btn-primary">
                        {{ $unitId ? __('units.update_unit') : __('units.create_unit') }} <i class="bi bi-check"></i>
                    </button>
                </x-slot:footer>
            </x-form-card>
        </form>
    </div>
</div>
