<div>
    @if (session()->has('error'))
        <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-red-50 text-red-700 border-red-200 pr-12 fade show" role="alert">
            <div class="alert-body">
                <span>{{ session('error') }}</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        </div>
    @endif
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

    {{-- Explanation of how the barcode is generated --}}
    <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-indigo-50 text-indigo-700 border-indigo-200">
        <p class="mb-0 small">{{ __('product.barcode_generation_explanation') }}</p>
    </div>

    {{-- Products queued for printing --}}
    <div class="card">
        <div class="flex-auto p-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold">{{ __('product.selected_products') }}</span>
                @if(!empty($items))
                    <button wire:click="clearItems" type="button" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i> {{ __('product.clear_all') }}
                    </button>
                @endif
            </div>

            @if(!empty($items))
                <ul class="list-group list-group-flush mb-0">
                    @foreach($items as $productId => $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-bold text-truncate">{{ $item['product']->product_name }}</div>
                                <div class="small text-muted"><i class="bi bi-upc-scan"></i> {{ $item['product']->product_code }}</div>
                            </div>
                            <input wire:model.live="items.{{ $productId }}.quantity"
                                   class="form-control w-auto" type="number" min="1" max="100"
                                   style="max-width: 90px;"
                                   title="{{ __('product.quantity') }}">
                            <button wire:click="removeItem('{{ $productId }}')" type="button" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-center text-muted mb-0">{{ __('product.no-products-found') }}</p>
            @endif

            {{-- Label configuration --}}
            <div class="mt-3">
                <span class="fw-bold d-block mb-2">{{ __('product.label_configuration') }}</span>
                <div class="d-flex flex-wrap gap-3">
                    <div class="form-check">
                        <input wire:model.live="showName" class="form-check-input" type="checkbox" id="barcode-show-name">
                        <label class="form-check-label" for="barcode-show-name">{{ __('product.show_name') }}</label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="showPrice" class="form-check-input" type="checkbox" id="barcode-show-price">
                        <label class="form-check-label" for="barcode-show-price">{{ __('product.show_price') }}</label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="showCode" class="form-check-input" type="checkbox" id="barcode-show-code">
                        <label class="form-check-label" for="barcode-show-code">{{ __('product.show_code') }}</label>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button wire:click="generateBarcodes" type="button" class="btn btn-primary" @disabled(empty($items))>
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

    @if(!empty($labels))
        <div class="text-right my-3">
            <button wire:click="getPdf" wire:loading.attr="disabled" type="button" class="btn btn-primary">
                <span wire:loading wire:target="getPdf" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <i wire:loading.remove wire:target="getPdf" class="bi bi-file-earmark-pdf"></i> {{ __('product.download-pdf') }}
            </button>
        </div>
        <div class="card">
            <div class="flex-auto p-2">
                <div class="row justify-content-center">
                    @foreach($labels as $label)
                        <div class="col-lg-3 col-md-4 col-sm-6 barcode-label">
                            @if($showName)
                                <p class="mt-3 mb-1 barcode-label-text">
                                    {{ $label['name'] }}
                                </p>
                            @endif
                            <div>
                                {!! $label['svg'] !!}
                            </div>
                            @if($showCode)
                                <p class="barcode-label-text mb-1">
                                    {{ $label['code'] }}
                                </p>
                            @endif
                            @if($showPrice)
                                <p class="barcode-label-text">
                                    {{ __('product.price') }}: {{ format_currency($label['price']) }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
