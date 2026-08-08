<div>
    @if (session()->has('message'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
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
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="fw-bold">#{{ $key + 1 }} {{ $product['product_name'] ?? $product['product']['product_name'] }}</span>
                                <button type="button" class="btn btn-danger btn-sm" wire:click="removeProduct({{ $key }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><span class="fw-bold">{{ __('product.code') }}:</span> {{ $product['product_code'] ?? $product['product']['product_code'] }}</p>
                                <p class="mb-3">
                                    <span class="fw-bold">{{ __('product.stock') }}:</span>
                                    <span class="badge badge-info">
                                        {{ $product['product_quantity'] ?? $product['product']['product_quantity'] }} {{ $product['product_unit'] ?? $product['product']['product_unit'] }}
                                    </span>
                                </p>
                                <input type="hidden" name="product_ids[]" value="{{ $product['product']['id'] ?? $product['id'] }}">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('product.quantity') }}</label>
                                    <input type="number" name="quantities[]" min="1" class="form-control" value="{{ $product['quantity'] ?? 1 }}">
                                </div>
                                <div>
                                    <label class="form-label fw-bold">{{ __('product.type') }}</label>
                                    @if(isset($product['type']) && $product['type'] == 'sub')
                                        <select name="types[]" class="form-control">
                                            <option value="sub" selected>(-) {{ __('product.subtraction') }}</option>
                                            <option value="add">(+) {{ __('product.addition') }}</option>
                                        </select>
                                    @else
                                        <select name="types[]" class="form-control">
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
                    <div class="card"><div class="card-body text-center text-danger">{{ __('product.no-products-found') }}</div></div>
                </div>
            @endif
        </div>
    </div>
</div>
