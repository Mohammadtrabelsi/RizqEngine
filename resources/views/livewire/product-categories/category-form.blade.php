<form wire:submit="save">
    <div class="mb-4 text-center">
        @if ($image)
            <img src="{{ $image->temporaryUrl() }}" alt="{{ __('product.category_image') }}" class="thumb-cover img-thumbnail mb-2" width="100" height="100">
        @elseif ($existingImageUrl)
            <img src="{{ $existingImageUrl }}" alt="{{ __('product.category_image') }}" class="thumb-cover img-thumbnail mb-2" width="100" height="100">
        @endif
    </div>
    <div class="mb-4">
        <label class="font-weight-bold" for="image">{{ __('product.category_image') }}</label>
        <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('image') !border-red-500 @enderror" type="file" accept="image/*" wire:model="image">
        <div wire:loading wire:target="image"><small class="text-muted">{{ __('product.uploading_image') }}</small></div>
        @error('image') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
    </div>
    <div class="mb-4">
        <label class="font-weight-bold" for="category_code">{{ __('product.category_code') }} <span class="text-danger">*</span></label>
        <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('category_code') !border-red-500 @enderror" type="text" wire:model="category_code">
        @error('category_code') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
    </div>
    <div class="mb-4">
        <label class="font-weight-bold" for="category_name">{{ __('product.category_name') }} <span class="text-danger">*</span></label>
        <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('category_name') !border-red-500 @enderror" type="text" wire:model="category_name">
        @error('category_name') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
    </div>
    <div class="mb-4">
        <label class="font-weight-bold" for="description">{{ __('product.category_description') }}</label>
        <textarea class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('description') !border-red-500 @enderror" rows="3" wire:model="description"></textarea>
        @error('description') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
    </div>
    <div class="mb-4">
        <label class="font-weight-bold" for="color">{{ __('product.category_color') }}</label>
        <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 form-control-color @error('color') !border-red-500 @enderror" type="color" wire:model="color">
        @error('color') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
    </div>
    <div class="mb-4 form-check">
        <input class="form-check-input" type="checkbox" id="is_active" wire:model="is_active">
        <label class="form-check-label" for="is_active">{{ __('product.category_active') }}</label>
    </div>
    <div class="mb-4 mb-0">
        <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
            {{ $categoryId ? __('product.update_category') : __('app.create') }} <i class="bi bi-check"></i>
        </button>
    </div>
</form>
