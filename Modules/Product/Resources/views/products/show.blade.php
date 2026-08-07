@extends('layouts.app')

@section('title', 'Product Details')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
        <li class="breadcrumb-item active">Details</li>
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
                                <span class="fw-bold">Product Code</span><span>{{ $product->product_code }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Barcode Symbology</span><span>{{ $product->product_barcode_symbology }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Name</span><span>{{ $product->product_name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Category</span><span>{{ $product->category->category_name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Cost</span><span>{{ format_currency($product->product_cost) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Price</span><span>{{ format_currency($product->product_price) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Quantity</span><span>{{ $product->product_quantity . ' ' . $product->product_unit }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Stock Worth</span>
                                <span>
                                    COST:: {{ format_currency($product->product_cost * $product->product_quantity) }} /
                                    PRICE:: {{ format_currency($product->product_price * $product->product_quantity) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Alert Quantity</span><span>{{ $product->product_stock_alert }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Tax (%)</span><span>{{ $product->product_order_tax ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Tax Type</span>
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
                                <span class="fw-bold">Note</span><span>{{ $product->product_note ?? 'N/A' }}</span>
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



