<form wire:submit="save">
    <div class="form-group">
        <label class="font-weight-bold" for="category_code">{{ __('product.category_code') }} <span class="text-danger">*</span></label>
        <input class="form-control @error('category_code') is-invalid @enderror" type="text" wire:model="category_code">
        @error('category_code') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label class="font-weight-bold" for="category_name">{{ __('product.category_name') }} <span class="text-danger">*</span></label>
        <input class="form-control @error('category_name') is-invalid @enderror" type="text" wire:model="category_name">
        @error('category_name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
    </div>
    <div class="form-group mb-0">
        <button type="submit" class="btn btn-primary">
            {{ $categoryId ? __('product.update_category') : __('app.create') }} <i class="bi bi-check"></i>
        </button>
    </div>
</form>
