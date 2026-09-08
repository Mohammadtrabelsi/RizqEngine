<div>
    <div class="row mb-4">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="flex-auto p-2 p-3 d-flex align-items-center">
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
                <div class="flex-auto p-2 p-3 d-flex align-items-center">
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
                <div class="flex-auto p-2">
                    <div class="form-check mb-3">
                        <input wire:model.live="only_out_of_stock" type="checkbox" class="form-check-input" id="only_out_of_stock">
                        <label class="form-check-label" for="only_out_of_stock">{{ __('report.show-only-out-of-stock-products') }}</label>
                    </div>
                    <div class="block w-full overflow-x-auto">
                        <table class="w-full mb-4 text-slate-900 border-collapse [&_tbody_tr:hover]:bg-slate-50 align-middle mb-0">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th scope="col">{{ __('report.product') }}</th>
                                    <th scope="col">{{ __('report.reference') }}</th>
                                    <th scope="col">{{ __('report.category') }}</th>
                                    <th scope="col" class="text-end">{{ __('report.in-stock') }}</th>
                                    <th scope="col" class="text-end">{{ __('report.alert-level') }}</th>
                                    <th scope="col" class="text-center">{{ __('report.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td class="fw-bold">{{ $product->product_name }}</td>
                                        <td class="text-muted">{{ $product->product_code }}</td>
                                        <td>{{ optional($product->category)->category_name }}</td>
                                        <td class="text-end @if($product->product_quantity <= 0) text-danger fw-bold @endif">{{ $product->product_quantity }}</td>
                                        <td class="text-end">{{ $product->product_stock_alert }}</td>
                                        <td class="text-center">
                                            @if($product->product_quantity <= 0)
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-red-100 text-red-700">{{ __('report.out-of-stock') }}</span>
                                            @else
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-amber-100 text-amber-700">{{ __('report.low-stock') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">{{ __('report.all-products-above-alert-level') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div @class(['mt-3' => $products->hasPages()])>{{ $products->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
