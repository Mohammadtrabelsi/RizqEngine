<div>
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-warning p-3 mfe-3 rounded">
                        <i class="bi bi-exclamation-triangle font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-warning">{{ $lowStockCount }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.products-at-or-below-alert-level') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-danger p-3 mfe-3 rounded">
                        <i class="bi bi-x-octagon font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-danger">{{ $outOfStockCount }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.out-of-stock') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input wire:model.live="only_out_of_stock" type="checkbox" class="form-check-input" id="only_out_of_stock">
                        <label class="form-check-label" for="only_out_of_stock">{{ __('report.show-only-out-of-stock-products') }}</label>
                    </div>
                    <div class="row">
                        @forelse($products as $product)
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0">{{ $product->product_name }}</h6>
                                            @if($product->product_quantity <= 0)
                                                <span class="badge badge-danger">{{ __('report.out-of-stock') }}</span>
                                            @else
                                                <span class="badge badge-warning">{{ __('report.low-stock') }}</span>
                                            @endif
                                        </div>
                                        <p class="text-muted small mb-2">{{ $product->product_code }}</p>
                                        <ul class="list-group list-group-flush mb-0">
                                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.category') }}</span><span>{{ optional($product->category)->category_name }}</span></li>
                                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.in-stock') }}</span><span>{{ $product->product_quantity }}</span></li>
                                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.alert-level') }}</span><span>{{ $product->product_stock_alert }}</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center">{{ __('report.all-products-above-alert-level') }}</div>
                        @endforelse
                    </div>
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
