<div class="position-relative">
    <div class="card mb-0 border-0 shadow-sm">
        <div class="card-body">
            <div class="form-group mb-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="bi bi-search text-primary"></i>
                        </div>
                    </div>
                    <input wire:keydown.escape="resetQuery" wire:model.live.debounce.500ms="query" type="text" class="form-control" placeholder="{{ __('general.search-product') }}">
                </div>
            </div>

            <div class="row g-2 align-items-center">
                <div class="col-sm-7">
                    <select wire:model.live="category" class="form-select form-select-sm">
                        <option value="">{{ __('general.all-categories') }}</option>
                        @foreach($categories as $categoryOption)
                            <option value="{{ $categoryOption->id }}">{{ $categoryOption->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-5">
                    <div class="form-check">
                        <input wire:model.live="in_stock_only" class="form-check-input" type="checkbox" id="search-in-stock-only">
                        <label class="form-check-label small" for="search-in-stock-only">
                            {{ __('general.in-stock-only') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div wire:loading class="card position-absolute mt-1 border-0 search-product-results">
        <div class="card-body shadow">
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">{{ __('general.loading') }}...</span>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($query))
        <div wire:click="resetQuery" class="position-fixed w-100 h-100 search-product-backdrop"></div>
        @if($search_results->isNotEmpty())
            <div class="card position-absolute mt-1 search-product-results-front">
                <div class="card-body shadow p-2">
                    <ul class="list-group list-group-flush">
                        @foreach($search_results as $result)
                            <li class="list-group-item list-group-item-action px-2">
                                <a wire:click="resetQuery" wire:click.prevent="selectProduct({{ $result }})" href="#" class="d-flex align-items-center text-decoration-none text-reset gap-2">
                                    <img src="{{ $result->getFirstMediaUrl('images', 'thumb') }}"
                                         alt="{{ $result->product_name }}"
                                         width="40" height="40"
                                         class="rounded border flex-shrink-0" style="object-fit: cover;">
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-bold text-truncate">{{ $result->product_name }}</div>
                                        <div class="small text-muted">
                                            <span><i class="bi bi-upc-scan"></i> {{ $result->product_code }}</span>
                                            @if($result->category)
                                                <span class="ms-2"><i class="bi bi-tag"></i> {{ $result->category->category_name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <div class="fw-bold text-primary">{{ format_currency($result->product_price) }}</div>
                                        @if($result->product_quantity <= 0)
                                            <span class="badge bg-danger">{{ __('general.out-of-stock') }}</span>
                                        @elseif($result->product_quantity <= $result->product_stock_alert)
                                            <span class="badge bg-warning text-dark">{{ $result->product_quantity }} {{ $result->product_unit }}</span>
                                        @else
                                            <span class="badge bg-success">{{ $result->product_quantity }} {{ $result->product_unit }}</span>
                                        @endif
                                    </div>
                                </a>
                            </li>
                        @endforeach
                        @if($search_results->count() >= $how_many)
                             <li class="list-group-item list-group-item-action text-center">
                                 <a wire:click.prevent="loadMore" class="btn btn-primary btn-sm" href="#">
                                     {{ __('general.load-more') }} <i class="bi bi-arrow-down-circle"></i>
                                 </a>
                             </li>
                        @endif
                    </ul>
                </div>
            </div>
        @else
            <div class="card position-absolute mt-1 border-0 search-product-results">
                <div class="card-body shadow">
                    <div class="alert alert-warning mb-0">
                        {{ __('general.no-product-found') }}
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
