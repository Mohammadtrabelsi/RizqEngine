<div>
    <div class="card border-0 shadow-sm mb-3">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
            <h5 class="mb-0"><i class="bi bi-funnel text-primary"></i> {{ __('app.filters') }}</h5>
        </div>
        <div class="flex-auto p-2">
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
    </div>
</div>
