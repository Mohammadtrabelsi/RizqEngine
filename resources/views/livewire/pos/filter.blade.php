<div>
    <div class="form-row">
        <div class="col-md-7">
            <div class="form-group">
                <label>{{ __('filters.product-category') }}</label>
                <select wire:model.live="category" class="form-control">
                    <option value="">{{ __('filters.select-category') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label>{{ __('filters.product-count') }}</label>
                <select wire:model.live="showCount" class="form-control">
                    <option value="9">{{ __('filters.show-9') }}</option>
                    <option value="15">{{ __('filters.show-15') }}</option>
                    <option value="21">{{ __('filters.show-21') }}</option>
                    <option value="30">{{ __('filters.show-30') }}</option>
                    <option value="">{{ __('filters.show-all') }}</option>
                </select>
            </div>
        </div>
    </div>
</div>
