<div>
    <div class="form-row">
        <div class="col-md-7">
            <div class="mb-4">
                <label>{{ __('sale.product-category') }}</label>
                <select wire:model.live="category" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                    <option value="">{{ __('sale.select-category') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <div class="mb-4">
                <label>{{ __('sale.product-count') }}   </label>
                <select wire:model.live="showCount" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
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
