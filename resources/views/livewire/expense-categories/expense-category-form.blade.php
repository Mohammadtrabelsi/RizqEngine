<form wire:submit="save">
    <div class="form-group">
        <label for="category_name">{{ __('expense-category.category_name') }} <span class="text-danger">*</span></label>
        <input class="form-control @error('category_name') is-invalid @enderror" type="text" wire:model="category_name">
        @error('category_name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label for="category_description">{{ __('expense-category.description') }}</label>
        <textarea class="form-control @error('category_description') is-invalid @enderror" wire:model="category_description" rows="5"></textarea>
        @error('category_description') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
    </div>
    <div class="form-group mb-0">
        <button type="submit" class="btn btn-primary">
            {{ $categoryId ? __('app.update') : __('app.create') }} <i class="bi bi-check"></i>
        </button>
    </div>
</form>
