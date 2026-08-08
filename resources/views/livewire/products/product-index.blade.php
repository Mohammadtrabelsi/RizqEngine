<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                {{ __('product.add_product') }} <i class="bi bi-plus"></i>
            </a>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="row">
        @forelse($products as $product)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="product-{{ $product->id }}">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="{{ $product->getFirstMediaUrl('images', 'thumb') }}"
                             class="img-thumbnail mb-3" width="90" alt="{{ $product->product_name }}">
                        <h5 class="card-title mb-1">{{ $product->product_name }}</h5>
                        <p class="text-muted mb-2"><small>{{ $product->product_code }}</small></p>
                        <span class="badge bg-secondary mb-3">{{ optional($product->category)->category_name }}</span>
                        <ul class="list-group list-group-flush text-start mb-3">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Cost</span><span>{{ format_currency($product->product_cost) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Price</span><span>{{ format_currency($product->product_price) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Quantity</span><span>{{ $product->product_quantity . ' ' . $product->product_unit }}</span>
                            </li>
                        </ul>
                        <div class="btn-group">
                            @can('edit_products')
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-info btn-sm"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('show_products')
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i></a>
                            @endcan
                            @can('delete_products')
                                <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $product->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-muted">{{ __('product.no_products_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
</div>
