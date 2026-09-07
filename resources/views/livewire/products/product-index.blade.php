<div>
    @if (session()->has('success'))
        <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-emerald-50 text-emerald-700 border-emerald-200 pr-12 fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('info'))
        <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-indigo-50 text-indigo-700 border-indigo-200 pr-12 fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('warning'))
        <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-amber-50 text-amber-700 border-amber-200 pr-12 fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row align-items-center">
        <div class="col-12 col-md-4 mb-3">
            <a href="{{ route('products.create') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                <i class="bi bi-plus-circle"></i> {{ __('product.add_product') }}
            </a>
            @can('create_products')
            <a href="{{ route('products.import') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-indigo-600 border-indigo-600 hover:bg-indigo-600 hover:!text-white">
                <i class="bi bi-upload"></i> {{ __('nav.import_products') }}
            </a>
            @endcan
        </div>
        <div class="col-12 col-md-8 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="{{ __('app.search') }} products...">
        </div>
    </div>

    {{-- Expired stock manager --}}
    @can('edit_products')
        @if($this->expiredInStockCount > 0)
            <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-amber-50 text-amber-700 border-amber-200 d-flex flex-wrap align-items-center justify-content-between gap-2" role="alert">
                <span>
                    <i class="bi bi-exclamation-octagon-fill"></i>
                    {{ trans_choice('product.expired-in-stock-notice', $this->expiredInStockCount, ['count' => $this->expiredInStockCount]) }}
                </span>
                <button type="button"
                        class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-amber-500 !text-white border-amber-500 hover:bg-amber-600 !px-3 !py-1.5 !text-xs"
                        wire:click="markExpiredOutOfStock"
                        wire:confirm="{{ __('app.are_you_sure') }}">
                    <i class="bi bi-box-seam"></i> {{ __('product.mark_expired_out_of_stock') }}
                </button>
            </div>
        @endif
    @endcan

    {{-- Filters container --}}
    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm mb-4">
        <div class="flex-auto p-5">
            <h6 class="mb-3 text-lg font-semibold text-slate-900 text-muted mb-3">
                <i class="bi bi-funnel"></i> {{ __('app.filters') }}
            </h6>
            <div class="row align-items-end">
                <div class="col-12 col-md-3 mb-3">
                    <label class="inline-block mb-1.5 text-sm font-medium text-slate-900 small text-muted mb-1">{{ __('product.category') }}</label>
                    <select wire:model.live="categoryId" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 pr-9 text-sm leading-normal text-slate-900 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" aria-label="Filter by category">
                        <option value="">{{ __('app.all') }}</option>
                        @foreach($this->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <label class="inline-block mb-1.5 text-sm font-medium text-slate-900 small text-muted mb-1">{{ __('product.supplier') }}</label>
                    <select wire:model.live="supplierId" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 pr-9 text-sm leading-normal text-slate-900 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" aria-label="Filter by supplier">
                        <option value="">{{ __('app.all') }}</option>
                        @foreach($this->suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <label class="inline-block mb-1.5 text-sm font-medium text-slate-900 small text-muted mb-1">{{ __('product.stock') }}</label>
                    <select wire:model.live="stockStatus" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 pr-9 text-sm leading-normal text-slate-900 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" aria-label="Filter by stock status">
                        @foreach($this->stockStatusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <label class="inline-block mb-1.5 text-sm font-medium text-slate-900 small text-muted mb-1">{{ __('product.expiry') }}</label>
                    <select wire:model.live="expiry" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 pr-9 text-sm leading-normal text-slate-900 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" aria-label="Filter by expiry">
                        @foreach($this->expiryOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <label class="inline-block mb-1.5 text-sm font-medium text-slate-900 small text-muted mb-1">{{ __('product.min_price') }}</label>
                    <input type="number" min="0" step="0.01" wire:model.live.debounce.500ms="minPrice" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="0">
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <label class="inline-block mb-1.5 text-sm font-medium text-slate-900 small text-muted mb-1">{{ __('product.max_price') }}</label>
                    <input type="number" min="0" step="0.01" wire:model.live.debounce.500ms="maxPrice" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="∞">
                </div>
                <div class="col-12 col-md-6 mb-3 d-flex align-items-end">
                    <button type="button" wire:click="resetFilters" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-slate-600 border-slate-300 hover:bg-slate-50 hover:!text-slate-900 hover:border-slate-400 !px-3 !py-1.5 !text-xs">
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
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 h-100 border-0 shadow-sm card-lift">
                    <!-- Image Section -->
                    <div class="position-relative overflow-hidden media-thumb media-thumb--lg">
                        <img src="{{ $product->getFirstMediaUrl('images', 'thumb') }}"
                             class="w-100 h-100 thumb-cover" alt="{{ $product->product_name }}">
                        <!-- Stock Status Badge -->
                        <div class="position-absolute top-0 end-0 m-2 text-end">
                            <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-{{ $product->stock_status->color() }}">
                                <i class="bi bi-{{ $product->stock_status->icon() }}"></i> {{ $product->stock_status->label() }}
                            </span>
                            @if($product->is_expired)
                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-danger d-block mt-1">
                                    <i class="bi bi-calendar-x"></i> {{ __('product.expired') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex-auto p-5 d-flex flex-column">
                        <!-- Product Name & Code -->
                        <h5 class="mb-3 text-lg font-semibold text-slate-900 mb-1 text-truncate" title="{{ $product->product_name }}">{{ $product->product_name }}</h5>
                        <p class="text-muted mb-2">
                            <small><i class="bi bi-upc-scan"></i> {{ $product->product_code }}</small>
                        </p>

                        <!-- Category & Supplier -->
                        <div class="mb-3">
                            <div class="mb-1">
                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-secondary">
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
                            <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-indigo-50 text-indigo-700 border-indigo-200 py-2 px-3 mb-3">
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
                                <a href="{{ route('products.edit', $product->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-indigo-600 border-indigo-600 hover:bg-indigo-600 hover:!text-white !px-3 !py-1.5 !text-xs" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endcan
                            @can('show_products')
                                <a href="{{ route('products.show', $product->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-indigo-600 border-indigo-600 hover:bg-indigo-600 hover:!text-white !px-3 !py-1.5 !text-xs" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                            @endcan
                            @can('delete_products')
                                <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-red-500 border-red-500 hover:bg-red-500 hover:!text-white !px-3 !py-1.5 !text-xs" wire:click="delete({{ $product->id }})" wire:confirm="{{ __('app.are_you_sure') }}" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                    <div class="flex-auto p-5 text-center py-5">
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
