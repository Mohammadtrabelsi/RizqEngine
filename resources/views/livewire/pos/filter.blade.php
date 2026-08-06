<div>
    <div class="form-row">
        <div class="col-md-7">
            <div class="form-group">
                <label>{{ __('sale.product-category') }}</label>
                <select wire:model.live="category" class="form-control">
                    <option value="">{{ __('sale.select-category') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label>{{ __('sale.product-count') }}   </label>
                <select wire:model.live="showCount" class="form-control">
                    <option value="9">{{ __('sale.show-9-products') }}</option>
                    <option value="15">{{ __('sale.show-15-products') }}</option>
                    <option value="21">{{ __('sale.show-21-products') }}</option>
                    <option value="30">{{ __('sale.show-30-products') }}</option>
                    <option value="">{{ __('sale.show-all-products') }}</option>
                </select>
            </div>
        </div>
    </div>
</div>
