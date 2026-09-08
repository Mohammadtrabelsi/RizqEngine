<div>
    @if (session()->has('message'))
        <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-amber-50 text-amber-700 border-amber-200 pr-12 fade show" role="alert">
            <div class="alert-body">
                <span>{{ session('message') }}</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        </div>
    @endif
    <div class="position-relative">
        <div wire:loading.flex class="col-12 position-absolute justify-content-center align-items-center wire-loading-overlay">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">{{ __('product.loading') }}</span>
            </div>
        </div>
        <div class="row">
            @if(!empty($products))
                @foreach($products as $key => $product)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 h-100">
                            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
                                <span class="fw-bold">#{{ $key + 1 }} {{ translatable_string($product['product_name'] ?? $product['product']['product_name']) }}</span>
                                <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-red-500 !text-white border-red-500 hover:bg-red-600 hover:border-red-600 !px-3 !py-1.5 !text-xs" wire:click="removeProduct({{ $key }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="flex-auto p-2">
                                <p class="mb-2"><span class="fw-bold">{{ __('product.code') }}:</span> {{ $product['product_code'] ?? $product['product']['product_code'] }}</p>
                                <p class="mb-3">
                                    <span class="fw-bold">{{ __('product.stock') }}:</span>
                                    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-cyan-100 text-cyan-700">
                                        {{ $product['product_quantity'] ?? $product['product']['product_quantity'] }} {{ $product['product_unit'] ?? $product['product']['product_unit'] }}
                                    </span>
                                </p>
                                <input type="hidden" name="product_ids[]" value="{{ $product['product']['id'] ?? $product['id'] }}">
                                <div class="mb-3">
                                    <label class="inline-block mb-1.5 text-sm font-medium text-slate-900 fw-bold">{{ __('product.quantity') }}</label>
                                    <input type="number" name="quantities[]" min="1" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" value="{{ $product['quantity'] ?? 1 }}">
                                </div>
                                <div>
                                    <label class="inline-block mb-1.5 text-sm font-medium text-slate-900 fw-bold">{{ __('product.type') }}</label>
                                    @if(isset($product['type']) && $product['type'] == 'sub')
                                        <select name="types[]" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                                            <option value="sub" selected>(-) {{ __('product.subtraction') }}</option>
                                            <option value="add">(+) {{ __('product.addition') }}</option>
                                        </select>
                                    @else
                                        <select name="types[]" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                                            <option value="add">(+) {{ __('product.addition') }}</option>
                                            <option value="sub">(-) {{ __('product.subtraction') }}</option>
                                        </select>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900"><div class="flex-auto p-2 text-center text-danger">{{ __('product.no-products-found') }}</div></div>
                </div>
            @endif
        </div>
    </div>
</div>
