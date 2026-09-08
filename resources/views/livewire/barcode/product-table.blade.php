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
    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
        <div class="flex-auto p-2">
            @if(!empty($product))
                <ul class="list-group list-group-flush mb-0">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="fw-bold">{{ __('product.name') }}</span>
                        <span>{{ $product->product_name }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="fw-bold">{{ __('product.code') }}</span>
                        <span>{{ $product->product_code }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center barcode-cell">
                        <span class="fw-bold">
                            {{ __('product.quantity') }} <i class="bi bi-question-circle-fill text-info" data-toggle="tooltip" data-placement="top" title="Max Quantity: 100"></i>
                        </span>
                        <input wire:model.live="quantity" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 w-auto" type="number" min="1" max="100" value="{{ $quantity }}">
                    </li>
                </ul>
            @else
                <p class="text-center text-danger mb-0">{{ __('product.no-products-found') }}</p>
            @endif
            <div class="mt-3">
                <button wire:click="generateBarcodes({{ $product }}, {{ $quantity }})" type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                    <i class="bi bi-upc-scan"></i> {{ __('product.generate-barcodes') }}
                </button>
            </div>
        </div>
    </div>

    <div wire:loading wire:target="generateBarcodes" class="w-100">
        <div class="d-flex justify-content-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">{{ __('product.loading') }}</span>
            </div>
        </div>
    </div>

    @if(!empty($barcodes))
        <div class="text-right mb-3">
            <button wire:click="getPdf" wire:loading.attr="disabled" type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                <span wire:loading wire:target="getPdf" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <i wire:loading.remove wire:target="getPdf" class="bi bi-file-earmark-pdf"></i> {{ __('product.download-pdf') }}
            </button>
        </div>
        <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
            <div class="flex-auto p-2">
                <div class="row justify-content-center">
                    @foreach($barcodes as $barcode)
                        <div class="col-lg-3 col-md-4 col-sm-6 barcode-label">
                            <p class="mt-3 mb-1 barcode-label-text">
                                {{ $product->product_name }}
                            </p>
                            <div>
                                {!! $barcode !!}
                            </div>
                            <p class="barcode-label-text">
                                Price:: {{ format_currency($product->product_price) }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
