<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                <div class="flex-auto p-5 p-3 d-flex align-items-center">
                    <div class="bg-info p-3 mfe-3 rounded">
                        <i class="bi bi-arrow-up-circle font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-info">{{ $highStockCount }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.products-above-high-stock-level') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                <div class="flex-auto p-5">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th scope="col">{{ __('report.product') }}</th>
                                    <th scope="col">{{ __('report.reference') }}</th>
                                    <th scope="col">{{ __('report.category') }}</th>
                                    <th scope="col" class="text-end">{{ __('report.in-stock') }}</th>
                                    <th scope="col" class="text-end">{{ __('report.high-stock-level') }}</th>
                                    <th scope="col" class="text-center">{{ __('report.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td class="fw-bold">{{ $product->product_name }}</td>
                                        <td class="text-muted">{{ $product->product_code }}</td>
                                        <td>{{ optional($product->category)->category_name }}</td>
                                        <td class="text-end text-info fw-bold">{{ $product->product_quantity }}</td>
                                        <td class="text-end">{{ $product->product_stock_alert_max }}</td>
                                        <td class="text-center">
                                            <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-cyan-100 text-cyan-700">{{ __('report.high-stock') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">{{ __('report.no-products-above-high-stock-level') }}</td>
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
