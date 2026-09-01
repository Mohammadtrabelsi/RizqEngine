<div>
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-body">
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
                        <div class="pos-card card border-0 shadow h-100 transition-all">
                            <div class="pos-thumb position-relative overflow-hidden">
                                <img src="{{ $product->getFirstMediaUrl('images') }}" class="thumb-cover card-img-top h-100 w-100" alt="Product Image">
                                <!-- Stock Badge -->
                                <div class="badge mb-3 position-absolute" style="left:10px;top: 10px;
                                    @if($product->product_quantity <= $product->product_stock_alert)
                                        background-color: #dc3545;
                                    @elseif($product->product_quantity <= ($product->product_stock_alert * 2))
                                        background-color: #ffc107;
                                    @else
                                        background-color: #28a745;
                                    @endif
                                ">
                                    <i class="bi bi-box2"></i> {{ $product->product_quantity }} {{ $product->product_unit }}
                                </div>
                                <!-- Stock Status Overlay -->
                                @if($product->product_quantity <= $product->product_stock_alert)
                                    <div class="pos-card-badge position-absolute top-50 start-50 translate-middle badge bg-danger">
                                        <i class="bi bi-exclamation-triangle"></i> Low Stock
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <h6 class="card-title mb-1 text-truncate" title="{{ $product->product_name }}">{{ $product->product_name }}</h6>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <span class="badge bg-primary">{{ $product->product_code }}</span>
                                        @if($product->category)
                                            <span class="badge bg-secondary">{{ substr($product->category->category_name, 0, 10) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <p class="card-text mb-1">
                                        <small class="text-muted">Price:</small>
                                    </p>
                                    <p class="pos-card-price card-text font-weight-bold mb-2">{{ format_currency($product->product_price) }}</p>
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
                        <div class="alert alert-warning mb-0">
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
