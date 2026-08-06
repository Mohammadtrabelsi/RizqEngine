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
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('report.product') }}</th>
                                    <th>{{ __('report.category') }}</th>
                                    <th class="text-right">{{ __('report.in-stock') }}</th>
                                    <th class="text-right">{{ __('report.alert-level') }}</th>
                                    <th class="text-center">{{ __('report.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>{{ $product->product_name }} <span class="text-muted small">{{ $product->product_code }}</span></td>
                                        <td>{{ optional($product->category)->category_name }}</td>
                                        <td class="text-right">{{ $product->product_quantity }}</td>
                                        <td class="text-right">{{ $product->product_stock_alert }}</td>
                                        <td class="text-center">
                                            @if($product->product_quantity <= 0)
                                                <span class="badge badge-danger">{{ __('report.out-of-stock') }}</span>
                                            @else
                                                <span class="badge badge-warning">{{ __('report.low-stock') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ __('report.all-products-above-alert-level') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
