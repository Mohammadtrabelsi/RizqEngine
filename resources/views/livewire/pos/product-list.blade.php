<div>
    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm mt-3">
        <div class="flex-auto p-2">
            <livewire:pos.filter :categories="$categories"/>
            <div class="d-flex justify-content-center mb-3">{{ $products->links('pagination::bootstrap-5') }}</div>
            <div class="row position-relative">
                <div wire:loading.flex class="col-12 position-absolute justify-content-center align-items-center wire-loading-overlay">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">{{ __('product.loading') }}...</span>
                    </div>
                </div>
                @forelse($products as $product)
                    <div wire:click.prevent="selectProduct({{ $product }})" class="col-lg-4 col-md-6 col-xl-3 cursor-pointer mb-3">
                        <div class="pos-card relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow h-100 transition-all">
                            <div class="pos-thumb position-relative overflow-hidden">
                                <img src="{{ $product->getFirstMediaUrl('images') }}" class="thumb-cover card-img-top h-100 w-100" alt="Product Image">
                                <!-- Stock Badge -->
                                <div @class([
                                    'badge mb-3 position-absolute pos-stock-badge',
                                    'bg-danger' => $product->product_quantity <= $product->product_stock_alert,
                                    'bg-warning' => $product->product_quantity > $product->product_stock_alert && $product->product_quantity <= $product->product_stock_alert * 2,
                                    'bg-success' => $product->product_quantity > $product->product_stock_alert * 2,
                                ])>
                                    <i class="bi bi-box2"></i> {{ $product->product_quantity }} {{ $product->product_unit }}
                                </div>
                                <!-- Stock Status Overlay -->
                                @if($product->product_quantity <= $product->product_stock_alert)
                                    <div class="pos-card-badge position-absolute top-50 start-50 translate-middle inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-danger">
                                        <i class="bi bi-exclamation-triangle"></i> Low Stock
                                    </div>
                                @endif
                            </div>
                            <div class="flex-auto p-2">
                                <div class="mb-2">
                                    <h6 class="mb-3 text-lg font-semibold text-slate-900 mb-1 text-truncate" title="{{ $product->product_name }}">{{ $product->product_name }}</h6>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-primary">{{ $product->product_code }}</span>
                                        @if($product->category)
                                            <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-secondary">{{ substr($product->category->category_name, 0, 10) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <p class="mb-0 mb-1">
                                        <small class="text-muted">Price:</small>
                                    </p>
                                    <p class="pos-card-price mb-0 font-weight-bold mb-2">{{ format_currency($product->product_price) }}</p>
                                </div>
                                @if($product->product_cost > 0)
                                    <div class="small text-muted border-top pt-2">
                                        <i class="bi bi-percent"></i> Margin: {{ number_format((($product->product_price - $product->product_cost) / $product->product_price * 100), 1) }}%
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-amber-50 text-amber-700 border-amber-200 mb-0">
                            <i class="bi bi-info-circle"></i> {{ __('product.products-not-found') }}
                        </div>
                    </div>
                @endforelse
            </div>
            <div @class(['mt-3' => $products->hasPages()])>
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

</div>
