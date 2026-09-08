<div>
    <div class="position-relative">
        <div wire:loading.flex class="col-12 position-absolute justify-content-center align-items-center wire-loading-overlay">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">{{ __('product.loading') }}</span>
            </div>
        </div>
        <div class="row">
            @if(!empty($products))
                @foreach($products as $key => $product)
                    <div class="col-lg-4 col-md-6 mb-4" wire:key="exit-line-{{ $key }}">
                        <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 h-100">
                            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
                                <span class="fw-bold">#{{ $key + 1 }} {{ translatable_string($product['product_name']) }}</span>
                                <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-red-500 !text-white border-red-500 hover:bg-red-600 hover:border-red-600 !px-3 !py-1.5 !text-xs" wire:click="removeProduct({{ $key }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="flex-auto p-2">
                                <p class="mb-2"><span class="fw-bold">{{ __('product.code') }}:</span> {{ $product['product_code'] }}</p>
                                @php($stock = (int) ($product['product_quantity'] ?? 0))
                                <p class="mb-3">
                                    <span class="fw-bold">{{ __('product.stock') }}:</span>
                                    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline {{ $stock > 0 ? 'badge-info' : 'badge-danger bg-danger' }}">
                                        {{ $stock }} {{ $product['product_unit'] ?? '' }}
                                    </span>
                                </p>
                                <input type="hidden" name="product_ids[]" value="{{ $product['id'] }}">
                                <div class="mb-1">
                                    <label class="inline-block mb-1.5 text-sm font-medium text-slate-900 fw-bold">{{ __('stockexit.quantity_out') }}</label>
                                    <input type="number" name="quantities[]" min="1" @if($stock > 0) max="{{ $stock }}" @endif class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" value="1" required @disabled($stock <= 0)>
                                    @if($stock <= 0)
                                        <small class="text-danger d-block mt-1">{{ __('stockexit.out_of_stock') }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900"><div class="flex-auto p-2 text-center text-danger">{{ __('stockexit.no_products_selected') }}</div></div>
                </div>
            @endif
        </div>
    </div>
</div>
