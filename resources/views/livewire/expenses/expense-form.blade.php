<form wire:submit="save">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    {{ $expenseId ? __('expense.update') : __('expense.create') }} <i class="bi bi-check"></i>
                </button>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="reference">{{ __('expense.reference') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="reference" readonly>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="date">{{ __('expense.date') }} <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" wire:model="date">
                                @error('date') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="category_id">{{ __('expense.category') }} <span class="text-danger">*</span></label>
                                <select wire:model="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="amount">{{ __('expense.amount') }} <span class="text-danger">*</span></label>
                                <input id="amount" type="number" step="any" class="form-control @error('amount') is-invalid @enderror" wire:model="amount">
                                @error('amount') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="details">{{ __('expense.details') }}</label>
                        <textarea class="form-control" rows="6" wire:model="details"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
