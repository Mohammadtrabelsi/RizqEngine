{{-- Full-page Livewire component. --}}
<form wire:submit="save">
    <div class="row">
        <div class="col-12 col-lg-8 mb-4">
            <x-form-card :title="$expenseId ? __('expense.update') : __('expense.create')" icon="bi-receipt">
                <div class="form-row">
                    <div class="col-12 col-lg-6">
                        <div class="mb-4">
                            <label for="reference">{{ __('expense.reference') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="reference" readonly>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="mb-4">
                            <label for="date">{{ __('expense.date') }} <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') !border-red-500 @enderror" wire:model="date">
                            @error('date') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                    <div class="form-row">
                        <div class="col-12 col-lg-6">
                            <div class="mb-4">
                                <label for="category_id">{{ __('expense.category') }} <span class="text-danger">*</span></label>
                                <select wire:model="category_id" id="category_id" class="form-control @error('category_id') !border-red-500 @enderror">
                                    <option value="">{{ __('app.select') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="mb-4 mb-0">
                                <label for="amount">{{ __('expense.amount') }} <span class="text-danger">*</span></label>
                                <input id="amount" type="number" step="any" class="form-control @error('amount') !border-red-500 @enderror" wire:model="amount">
                                @error('amount') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-12 col-lg-6">
                            <div class="mb-4 mb-lg-0">
                                <label for="driver_id">{{ __('expense.driver') }}</label>
                                <select wire:model="driver_id" id="driver_id" class="form-control @error('driver_id') !border-red-500 @enderror">
                                    <option value="">{{ __('app.select') }}</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }}{{ $driver->phone ? ' — '.$driver->phone : '' }}</option>
                                    @endforeach
                                </select>
                                @error('driver_id') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="mb-0">
                                <label for="vehicle_id">{{ __('expense.vehicle') }}</label>
                                <select wire:model="vehicle_id" id="vehicle_id" class="form-control @error('vehicle_id') !border-red-500 @enderror">
                                    <option value="">{{ __('app.select') }}</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->label }}</option>
                                    @endforeach
                                </select>
                                @error('vehicle_id') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </x-form-card>
            </div>
        </div>
        <div class="col-12 col-lg-4 mb-4">
            <x-form-card :title="__('expense.details')" icon="bi-card-text">
                <div class="mb-4 mb-0">
                    <label for="details">{{ __('expense.details') }}</label>
                    <textarea class="form-control" rows="8" wire:model="details"></textarea>
                </div>
            </x-form-card>
        </div>
    </div>
</form>
