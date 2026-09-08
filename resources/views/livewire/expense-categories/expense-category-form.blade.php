<form wire:submit="save">
    <div class="mb-4">
        <label for="category_name">{{ __('expense-category.category_name') }} <span class="text-danger">*</span></label>
        <input class="form-control @error('category_name') !border-red-500 @enderror" type="text" wire:model="category_name">
        @error('category_name') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
    </div>
    <div class="mb-4">
        <label for="category_description">{{ __('expense-category.description') }}</label>
        <textarea class="form-control @error('category_description') !border-red-500 @enderror" wire:model="category_description" rows="5"></textarea>
        @error('category_description') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
    </div>
    <div class="mb-4 mb-0">
        <button type="submit" class="btn btn-primary">
            {{ $categoryId ? __('app.update') : __('app.create') }} <i class="bi bi-check"></i>
        </button>
    </div>
</form>
