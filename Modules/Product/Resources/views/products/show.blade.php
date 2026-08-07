@extends('layouts.app')

@section('title', __('product.product_details'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('product.products') }}</a></li>
        <li class="breadcrumb-item active">{{ __('product.product_details') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <div class="row mb-3">
            <div class="col-md-12">
                {!! \Milon\Barcode\Facades\DNS1DFacade::getBarCodeSVG($product->product_code, $product->product_barcode_symbology, 2, 110) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-lg-9">
                <div class="card h-100">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('product.product_code') }}</span><span>{{ $product->product_code }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('product.barcode_symbology') }}</span><span>{{ $product->product_barcode_symbology }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('product.product_name') }}</span><span>{{ $product->product_name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('product.category') }}</span><span>{{ $product->category->category_name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('product.product_cost') }}</span><span>{{ format_currency($product->product_cost) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('product.product_price') }}</span><span>{{ format_currency($product->product_price) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('product.product_quantity') }}</span><span>{{ $product->product_quantity . ' ' . $product->product_unit }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('product.stock_worth') }}</span>
                                <span>
                                    COST:: {{ format_currency($product->product_cost * $product->product_quantity) }} /
                                    PRICE:: {{ format_currency($product->product_price * $product->product_quantity) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('product.alert_quantity') }}</span><span>{{ $product->product_stock_alert }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('product.tax') }} (%)</span><span>{{ $product->product_order_tax ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('product.tax_type') }}</span>
                                <span>
                                    @if($product->product_tax_type == 1)
                                        Exclusive
                                    @elseif($product->product_tax_type == 2)
                                        Inclusive
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('product.note') }}</span><span>{{ $product->product_note ?? 'N/A' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        @forelse($product->getMedia('images') as $media)
                            <img src="{{ $media->getUrl() }}" alt="Product Image" class="img-fluid img-thumbnail mb-2">
                        @empty
                            <img src="{{ $product->getFirstMediaUrl('images') }}" alt="Product Image" class="img-fluid img-thumbnail mb-2">
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



