@extends('layouts.app')

@section('title', __('product.products'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('product.products') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    {{ __('product.add_product') }} <i class="bi bi-plus"></i>
                </a>
            </div>
        </div>

        <div class="row">
            @forelse($products as $product)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
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
                                @include('product.products.partials.actions', ['data' => $product])
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
            {{ $products->links('pagination.bootstrap-5') }}
        </div>
    </div>
@endsection
