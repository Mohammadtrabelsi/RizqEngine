{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <form wire:submit="save">
            <div class="row">
                <div class="col-12">
                    <div class="mb-4">
                        <button type="submit" class="btn btn-primary">
                            {{ $categoryId ? __('product.update_category') : __('app.create') }} <i class="bi bi-check"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12 col-lg-8 mb-4">
                    <div class="card">
                        <div class="flex-auto p-2">
                            <div class="mb-4">
                                <label class="font-weight-bold" for="category_code">{{ __('product.category_code') }} <span class="text-danger">*</span></label>
                                <input class="form-control @error('category_code') !border-red-500 @enderror" type="text" wire:model="category_code" disabled>
                                <small class="block mt-1 text-xs text-slate-500 text-muted">{{ __('product.category_code_auto_help') }}</small>
                                @error('category_code') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-4">
                                <label class="font-weight-bold" for="category_name">{{ __('product.category_name') }} <span class="text-danger">*</span></label>
                                <input class="form-control @error('category_name') !border-red-500 @enderror" type="text" wire:model="category_name">
                                @error('category_name') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-4">
                                <label class="font-weight-bold" for="description">{{ __('product.category_description') }}</label>
                                <textarea class="form-control @error('description') !border-red-500 @enderror" rows="3" wire:model="description"></textarea>
                                @error('description') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-4">
                                <label class="font-weight-bold" for="color">{{ __('product.category_color') }}</label>
                                <input class="form-control form-control-color @error('color') !border-red-500 @enderror" type="color" wire:model="color">
                                @error('color') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-4 mb-0 form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" wire:model="is_active">
                                <label class="form-check-label" for="is_active">{{ __('product.category_active') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 mb-4">
                    <div class="card">
                        <div class="flex-auto p-2">
                            <div class="mb-4 text-center">
                                @if ($image)
                                    <img src="{{ $image->temporaryUrl() }}" alt="{{ __('product.category_image') }}" class="thumb-cover img-thumbnail mb-2" width="100" height="100">
                                @elseif ($existingImageUrl)
                                    <img src="{{ $existingImageUrl }}" alt="{{ __('product.category_image') }}" class="thumb-cover img-thumbnail mb-2" width="100" height="100">
                                @endif
                            </div>
                            <div class="mb-4 mb-0">
                                <label class="font-weight-bold" for="image">{{ __('product.category_image') }}</label>
                                <input class="form-control @error('image') !border-red-500 @enderror" type="file" accept="image/*" wire:model="image">
                                <div wire:loading wire:target="image"><small class="text-muted">{{ __('product.uploading_image') }}</small></div>
                                @error('image') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
