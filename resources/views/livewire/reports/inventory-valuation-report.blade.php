<div>
    <div class="row">
        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-primary p-3 mfe-3 rounded">
                        <i class="bi bi-box-seam font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-primary">{{ $totalQuantity }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.units-in-stock') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-info p-3 mfe-3 rounded">
                        <i class="bi bi-cash-stack font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-info">{{ format_currency($totalCostValue) }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.value-at-cost') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-success p-3 mfe-3 rounded">
                        <i class="bi bi-tags font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-success">{{ format_currency($totalRetailValue) }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.value-at-retail') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-warning p-3 mfe-3 rounded">
                        <i class="bi bi-graph-up-arrow font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-warning">{{ format_currency($potentialProfit) }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.potential-profit') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="form-row mb-3">
                        <div class="col-lg-4">
                            <input wire:model.live="search" type="text" class="form-control" placeholder="{{ __('report.search-by-name-or-code') }}">
                        </div>
                        <div class="col-lg-4">
                            <select wire:model.live="category_id" class="form-control">
                                <option value="">{{ __('report.select-category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('report.product') }}</th>
                                    <th>{{ __('report.category') }}</th>
                                    <th class="text-right">{{ __('report.quantity') }}</th>
                                    <th class="text-right">{{ __('report.unit-cost') }}</th>
                                    <th class="text-right">{{ __('report.stock-value-cost') }}</th>
                                    <th class="text-right">{{ __('report.stock-value-retail') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>{{ $product->product_name }} <span class="text-muted small">{{ $product->product_code }}</span></td>
                                        <td>{{ optional($product->category)->category_name }}</td>
                                        <td class="text-right">{{ $product->product_quantity }}</td>
                                        <td class="text-right">{{ format_currency($product->product_cost) }}</td>
                                        <td class="text-right">{{ format_currency($product->product_quantity * $product->product_cost) }}</td>
                                        <td class="text-right">{{ format_currency($product->product_quantity * $product->product_price) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('report.no-products-found') }}</td>
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
