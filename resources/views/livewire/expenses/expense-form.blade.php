<form wire:submit="save">
    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                <button type="submit" class="btn btn-primary">
                    {{ $expenseId ? __('expense.update') : __('expense.create') }} <i class="bi bi-check"></i>
                </button>
            </div>
        </div>
        <div class="col-12 col-lg-8 mb-4">
            <div class="card">
                <div class="flex-auto p-2">
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
                                    <option value="">Select Category</option>
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
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4 mb-4">
            <div class="card">
                <div class="flex-auto p-2">
                    <div class="mb-4 mb-0">
                        <label for="details">{{ __('expense.details') }}</label>
                        <textarea class="form-control" rows="8" wire:model="details"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
