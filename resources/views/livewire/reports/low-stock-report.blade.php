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
                        <div class="text-uppercase font-weight-bold small">Products At Or Below Alert Level</div>
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
                        <div class="text-uppercase font-weight-bold small">Out Of Stock</div>
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
                        <label class="form-check-label" for="only_out_of_stock">Show only out-of-stock products</label>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th class="text-right">In Stock</th>
                                    <th class="text-right">Alert Level</th>
                                    <th class="text-center">Status</th>
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
                                                <span class="badge badge-danger">Out of stock</span>
                                            @else
                                                <span class="badge badge-warning">Low stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">All products are above their alert level.</td>
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
