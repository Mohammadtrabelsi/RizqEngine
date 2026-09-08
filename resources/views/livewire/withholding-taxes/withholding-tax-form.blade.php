{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <form wire:submit="save">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="flex-auto p-2">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="name">{{ __('withholding.name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') !border-red-500 @enderror" wire:model="name">
                                        @error('name') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="code">{{ __('withholding.code') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('code') !border-red-500 @enderror" wire:model="code">
                                        @error('code') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="rate">{{ __('withholding.rate') }} (%) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.001" min="0" max="100" class="form-control @error('rate') !border-red-500 @enderror" wire:model="rate">
                                        @error('rate') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="calculation_base">{{ __('withholding.calculation_base') }} <span class="text-danger">*</span></label>
                                        <select class="form-control @error('calculation_base') !border-red-500 @enderror" wire:model="calculation_base">
                                            @foreach($bases as $value => $labelKey)
                                                <option value="{{ $value }}">{{ __($labelKey) }}</option>
                                            @endforeach
                                        </select>
                                        <small class="block mt-1 text-xs text-slate-500 text-muted">{{ __('withholding.calculation_base_help') }}</small>
                                        @error('calculation_base') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-4">
                                        <label for="description">{{ __('withholding.description') }}</label>
                                        <textarea class="form-control @error('description') !border-red-500 @enderror" wire:model="description" rows="2"></textarea>
                                        @error('description') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="start_date">{{ __('withholding.start_date') }}</label>
                                        <input type="date" class="form-control @error('start_date') !border-red-500 @enderror" wire:model="start_date">
                                        @error('start_date') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="end_date">{{ __('withholding.end_date') }}</label>
                                        <input type="date" class="form-control @error('end_date') !border-red-500 @enderror" wire:model="end_date">
                                        @error('end_date') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" wire:model="applicable_to_purchases" id="applicable_to_purchases">
                                        <label class="form-check-label" for="applicable_to_purchases">{{ __('withholding.applicable_to_purchases') }}</label>
                                        @error('applicable_to_purchases') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" wire:model="applicable_to_sales" id="applicable_to_sales">
                                        <label class="form-check-label" for="applicable_to_sales">{{ __('withholding.applicable_to_sales') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" wire:model="active" id="active">
                                        <label class="form-check-label" for="active">{{ __('withholding.active') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 d-flex justify-content-end">
                                    <div class="mb-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ $withholdingTaxId ? __('withholding.update_withholding') : __('withholding.create_withholding') }} <i class="bi bi-check"></i>
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
