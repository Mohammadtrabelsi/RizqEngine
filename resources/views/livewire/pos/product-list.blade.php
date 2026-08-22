<div>
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-body">
            <livewire:pos.filter :categories="$categories"/>
            <div class="d-flex justify-content-center mb-3">{{ $products->links() }}</div>
            <div class="row position-relative">
                <div wire:loading.flex class="col-12 position-absolute justify-content-center align-items-center wire-loading-overlay">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">{{ __('product.loading') }}...</span>
                    </div>
                </div>
                @forelse($products as $product)
                    <div wire:click.prevent="selectProduct({{ $product }})" class="col-lg-4 col-md-6 col-xl-3 cursor-pointer mb-3">
                        <div class="card border-0 shadow h-100 transition-all" style="transition: all 0.3s ease;">
                            <div class="position-relative overflow-hidden" style="height: 200px;">
                                <img src="{{ $product->getFirstMediaUrl('images') }}" class="card-img-top h-100 w-100" style="object-fit: cover;" alt="Product Image">
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
                                    <div class="position-absolute top-50 start-50 translate-middle badge bg-danger" style="opacity: 0.9; font-size: 11px;">
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
                                    <p class="card-text font-weight-bold mb-2" style="font-size: 1.25rem; color: #2c3e50;">{{ format_currency($product->product_price) }}</p>
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
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <style>
        .cursor-pointer {
            cursor: pointer;
        }
        .cursor-pointer .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }
        .wire-loading-overlay {
            background-color: rgba(255, 255, 255, 0.8);
            z-index: 10;
            min-height: 300px;
        }
    </style>
</div>
