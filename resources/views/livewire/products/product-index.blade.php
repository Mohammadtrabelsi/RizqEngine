<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row align-items-center">
        <div class="col-12 col-md-4 mb-3">
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> {{ __('product.add_product') }}
            </a>
        </div>
        <div class="col-12 col-md-8 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }} products...">
        </div>
    </div>

    {{-- Expired stock manager --}}
    @can('edit_products')
        @if($this->expiredInStockCount > 0)
            <div class="alert alert-warning d-flex flex-wrap align-items-center justify-content-between gap-2" role="alert">
                <span>
                    <i class="bi bi-exclamation-octagon-fill"></i>
                    {{ trans_choice('product.expired-in-stock-notice', $this->expiredInStockCount, ['count' => $this->expiredInStockCount]) }}
                </span>
                <button type="button"
                        class="btn btn-warning btn-sm"
                        wire:click="markExpiredOutOfStock"
                        wire:confirm="{{ __('app.are_you_sure') }}">
                    <i class="bi bi-box-seam"></i> {{ __('product.mark_expired_out_of_stock') }}
                </button>
            </div>
        @endif
    @endcan

    {{-- Filters container --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="card-title text-muted mb-3">
                <i class="bi bi-funnel"></i> {{ __('app.filters') }}
            </h6>
            <div class="row align-items-end">
                <div class="col-12 col-md-3 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('product.category') }}</label>
                    <select wire:model.live="categoryId" class="form-select" aria-label="Filter by category">
                        <option value="">{{ __('app.all') }}</option>
                        @foreach($this->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('product.supplier') }}</label>
                    <select wire:model.live="supplierId" class="form-select" aria-label="Filter by supplier">
                        <option value="">{{ __('app.all') }}</option>
                        @foreach($this->suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('product.stock') }}</label>
                    <select wire:model.live="stockStatus" class="form-select" aria-label="Filter by stock status">
                        @foreach($this->stockStatusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('product.expiry') }}</label>
                    <select wire:model.live="expiry" class="form-select" aria-label="Filter by expiry">
                        @foreach($this->expiryOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('product.min_price') }}</label>
                    <input type="number" min="0" step="0.01" wire:model.live.debounce.500ms="minPrice" class="form-control" placeholder="0">
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('product.max_price') }}</label>
                    <input type="number" min="0" step="0.01" wire:model.live.debounce.500ms="maxPrice" class="form-control" placeholder="∞">
                </div>
                <div class="col-12 col-md-6 mb-3 d-flex align-items-end">
                    <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> {{ __('app.reset') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $products->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($products as $product)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="product-{{ $product->id }}">
                <div class="card h-100 border-0 shadow-sm card-lift">
                    <!-- Image Section -->
                    <div class="position-relative overflow-hidden media-thumb media-thumb--lg">
                        <img src="{{ $product->getFirstMediaUrl('images', 'thumb') }}"
                             class="w-100 h-100 thumb-cover" alt="{{ $product->product_name }}">
                        <!-- Stock Status Badge -->
                        <div class="position-absolute top-0 end-0 m-2 text-end">
                            <span class="badge bg-{{ $product->stock_status->color() }}">
                                <i class="bi bi-{{ $product->stock_status->icon() }}"></i> {{ $product->stock_status->label() }}
                            </span>
                            @if($product->is_expired)
                                <span class="badge bg-danger d-block mt-1">
                                    <i class="bi bi-calendar-x"></i> {{ __('product.expired') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body d-flex flex-column">
                        <!-- Product Name & Code -->
                        <h5 class="card-title mb-1 text-truncate" title="{{ $product->product_name }}">{{ $product->product_name }}</h5>
                        <p class="text-muted mb-2">
                            <small><i class="bi bi-upc-scan"></i> {{ $product->product_code }}</small>
                        </p>

                        <!-- Category & Supplier -->
                        <div class="mb-3">
                            <div class="mb-1">
                                <span class="badge bg-secondary">
                                    <i class="bi bi-tag"></i> {{ optional($product->category)->category_name }}
                                </span>
                            </div>
                            @if($product->supplier)
                                <p class="text-muted mb-0">
                                    <small><i class="bi bi-truck"></i> {{ $product->supplier->supplier_name }}</small>
                                </p>
                            @endif
                        </div>

                        <!-- Pricing Section -->
                        <div class="border-top pt-2 mb-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Cost</small>
                                    <span class="fw-bold text-success">{{ format_currency($product->product_cost) }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Price</small>
                                    <span class="fw-bold text-primary">{{ format_currency($product->product_price) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Profit Margin -->
                        @if($product->product_cost > 0)
                            <div class="alert alert-info py-2 px-3 mb-3">
                                <small>
                                    <i class="bi bi-percent"></i> Profit Margin: <strong>{{ number_format((($product->product_price - $product->product_cost) / $product->product_price * 100), 1) }}%</strong>
                                </small>
                            </div>
                        @endif

                        <!-- Stock Information -->
                        <div class="bg-light rounded p-2 mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Quantity</small>
                                <small class="fw-bold">{{ $product->product_quantity }} {{ $product->product_unit }}</small>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Stock Worth</small>
                                <small class="fw-bold text-primary">{{ format_currency($product->product_price * $product->product_quantity) }}</small>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Alert Level</small>
                                <small class="fw-bold">{{ $product->product_stock_alert }}</small>
                            </div>
                            @if($product->expiry_date)
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">{{ __('product.expiry_date') }}</small>
                                    <small class="fw-bold {{ $product->is_expired ? 'text-danger' : '' }}">
                                        {{ $product->expiry_date->format('Y-m-d') }}
                                    </small>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="btn-group w-100 mt-auto" role="group">
                            @can('edit_products')
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endcan
                            @can('show_products')
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary btn-sm" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                            @endcan
                            @can('delete_products')
                                <button type="button" class="btn btn-outline-danger btn-sm" wire:click="delete({{ $product->id }})" wire:confirm="{{ __('app.are_you_sure') }}" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox empty-state-icon"></i>
                        <p class="text-muted mt-3 mb-0">{{ __('product.no_products_found') }}</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
</div>
