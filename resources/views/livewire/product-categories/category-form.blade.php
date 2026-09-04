<form wire:submit="save">
    <div class="form-group text-center">
        @if ($image)
            <img src="{{ $image->temporaryUrl() }}" alt="{{ __('product.category_image') }}" class="thumb-cover img-thumbnail mb-2" width="100" height="100">
        @elseif ($existingImageUrl)
            <img src="{{ $existingImageUrl }}" alt="{{ __('product.category_image') }}" class="thumb-cover img-thumbnail mb-2" width="100" height="100">
        @endif
    </div>
    <div class="form-group">
        <label class="font-weight-bold" for="image">{{ __('product.category_image') }}</label>
        <input class="form-control @error('image') is-invalid @enderror" type="file" accept="image/*" wire:model="image">
        <div wire:loading wire:target="image"><small class="text-muted">{{ __('product.uploading_image') }}</small></div>
        @error('image') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
    </div>
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
    <div class="form-group">
        <label class="font-weight-bold" for="description">{{ __('product.category_description') }}</label>
        <textarea class="form-control @error('description') is-invalid @enderror" rows="3" wire:model="description"></textarea>
        @error('description') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label class="font-weight-bold" for="color">{{ __('product.category_color') }}</label>
        <input class="form-control form-control-color @error('color') is-invalid @enderror" type="color" wire:model="color">
        @error('color') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
    </div>
    <div class="form-group form-check">
        <input class="form-check-input" type="checkbox" id="is_active" wire:model="is_active">
        <label class="form-check-label" for="is_active">{{ __('product.category_active') }}</label>
    </div>
    <div class="form-group mb-0">
        <button type="submit" class="btn btn-primary">
            {{ $categoryId ? __('product.update_category') : __('app.create') }} <i class="bi bi-check"></i>
        </button>
    </div>
</form>
