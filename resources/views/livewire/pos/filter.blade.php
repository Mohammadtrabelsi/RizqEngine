<div>
    <div class="row align-items-end">
        <div class="col-12 col-lg-6 mb-3">
            <label class="form-label small text-muted mb-1">{{ __('sale.product-category') }}</label>
            <select wire:model.live="category" class="form-select">
                <option value="">{{ __('sale.select-category') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-lg-6 mb-3">
            <label class="form-label small text-muted mb-1">{{ __('sale.product-count') }}</label>
            <select wire:model.live="showCount" class="form-select">
                <option value="9">{{ __('sale.show-9-products') }}</option>
                <option value="15">{{ __('sale.show-15-products') }}</option>
                <option value="21">{{ __('sale.show-21-products') }}</option>
                <option value="30">{{ __('sale.show-30-products') }}</option>
                <option value="">{{ __('sale.show-all-products') }}</option>
            </select>
        </div>
    </div>
</div>
