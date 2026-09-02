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
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="fw-bold">#{{ $key + 1 }} {{ translatable_string($product['product_name']) }}</span>
                                <button type="button" class="btn btn-danger btn-sm" wire:click="removeProduct({{ $key }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><span class="fw-bold">{{ __('product.code') }}:</span> {{ $product['product_code'] }}</p>
                                @php($stock = (int) ($product['product_quantity'] ?? 0))
                                <p class="mb-3">
                                    <span class="fw-bold">{{ __('product.stock') }}:</span>
                                    <span class="badge {{ $stock > 0 ? 'badge-info' : 'badge-danger bg-danger' }}">
                                        {{ $stock }} {{ $product['product_unit'] ?? '' }}
                                    </span>
                                </p>
                                <input type="hidden" name="product_ids[]" value="{{ $product['id'] }}">
                                <div class="mb-1">
                                    <label class="form-label fw-bold">{{ __('stockexit.quantity_out') }}</label>
                                    <input type="number" name="quantities[]" min="1" @if($stock > 0) max="{{ $stock }}" @endif class="form-control" value="1" required @disabled($stock <= 0)>
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
                    <div class="card"><div class="card-body text-center text-danger">{{ __('stockexit.no_products_selected') }}</div></div>
                </div>
            @endif
        </div>
    </div>
</div>
