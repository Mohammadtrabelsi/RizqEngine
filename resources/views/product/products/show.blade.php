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
        <!-- Product Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-gradient">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h2 class="mb-2">
                                    <i class="bi bi-box-seam"></i> {{ $product->product_name }}
                                </h2>
                                <p class="mb-0"><small class="opacity-75">{{ __('product.product_code') }}: <strong>{{ $product->product_code }}</strong></small></p>
                                <p class="mb-0"><small class="opacity-75">{{ __('product.category') }}: <strong>{{ $product->category->category_name }}</strong></small></p>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="display-6 fw-bold">{{ format_currency($product->product_price) }}</div>
                                <small class="opacity-75">Selling Price</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Basic Information -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> {{ __('product.product_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-2">
                                    <label class="text-muted small"><i class="bi bi-upc-scan"></i> Barcode Symbology</label>
                                    <span class="fw-bold">{{ $product->product_barcode_symbology }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-2">
                                    <label class="text-muted small"><i class="bi bi-truck"></i> Supplier</label>
                                    <span class="fw-bold">{{ optional($product->supplier)->supplier_name ?? '<span class="text-muted">N/A</span>' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="text-muted small"><i class="bi bi-file-text"></i> Note</label>
                                    <span class="fw-bold small">{{ $product->product_note ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-2">
                                    <label class="text-muted small"><i class="bi bi-brightness-high"></i> Unit</label>
                                    <span class="fw-bold">{{ $product->product_unit }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Cost -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0"><i class="bi bi-cash-coin"></i> Pricing & Cost</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-1">Cost Price</small>
                                    <h4 class="mb-0 text-success">{{ format_currency($product->product_cost) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-1">Selling Price</small>
                                    <h4 class="mb-0 text-primary">{{ format_currency($product->product_price) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-percent"></i> Profit Margin: <strong>{{ $product->product_price > 0 ? number_format((($product->product_price - $product->product_cost) / $product->product_price * 100), 2) . '%' : 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Information -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0"><i class="bi bi-box2"></i> Stock Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-1">Current Quantity</small>
                                    <h4 class="mb-0">{{ $product->product_quantity }} <small class="text-muted">{{ $product->product_unit }}</small></h4>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-1">Alert Threshold</small>
                                    <h4 class="mb-0">{{ $product->product_stock_alert }} <small class="text-muted">{{ $product->product_unit }}</small></h4>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="text-muted small d-block mb-2"><i class="bi bi-graph-up"></i> Stock Worth</label>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="p-3 bg-success bg-opacity-10 rounded">
                                            <small class="text-muted d-block mb-1">By Cost Price</small>
                                            <h5 class="mb-0 text-success">{{ format_currency($product->product_cost * $product->product_quantity) }}</h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-3 bg-primary bg-opacity-10 rounded">
                                            <small class="text-muted d-block mb-1">By Selling Price</small>
                                            <h5 class="mb-0 text-primary">{{ format_currency($product->product_price * $product->product_quantity) }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tax Information -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0"><i class="bi bi-calculator"></i> Tax Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                                    <label class="text-muted small">Tax Rate</label>
                                    <span class="fw-bold">{{ $product->product_order_tax ?? 'N/A' }}%</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                                    <label class="text-muted small">Tax Type</label>
                                    <span class="badge bg-{{ $product->product_tax_type == 1 ? 'warning' : 'success' }}">
                                        @if($product->product_tax_type == 1)
                                            Exclusive
                                        @elseif($product->product_tax_type == 2)
                                            Inclusive
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Orders</h5>
                        <span class="badge bg-secondary">{{ $orders->count() }}</span>
                    </div>
                    <div class="card-body p-0">
                        @if($orders->isEmpty())
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                This product does not appear on any order yet.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Reference</th>
                                            <th>Customer</th>
                                            <th>Status</th>
                                            <th class="text-end">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders as $order)
                                            <tr>
                                                <td>
                                                    <span class="d-block">{{ optional($order['date'])->format('d M Y') }}</span>
                                                    <small class="text-muted">{{ optional($order['date'])->format('H:i') }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $order['badge'] }}">{{ $order['label'] }}</span>
                                                </td>
                                                <td>
                                                    @if($order['route'])
                                                        <a href="{{ $order['route'] }}">{{ $order['reference'] ?? '—' }}</a>
                                                    @else
                                                        {{ $order['reference'] ?? '—' }}
                                                    @endif
                                                </td>
                                                <td>{{ $order['party'] ?? '—' }}</td>
                                                <td><span class="badge bg-light text-dark text-capitalize">{{ $order['status'] ?? '—' }}</span></td>
                                                <td class="text-end">{{ $order['quantity'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Transaction History</h5>
                        <span class="badge bg-secondary">{{ $transactions->count() }}</span>
                    </div>
                    <div class="card-body p-0">
                        @if($transactions->isEmpty())
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                No transactions recorded for this product yet.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Reference</th>
                                            <th>Party</th>
                                            <th class="text-end">Quantity</th>
                                            <th class="text-end">Unit Price</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactions as $transaction)
                                            <tr>
                                                <td>
                                                    <span class="d-block">{{ optional($transaction['date'])->format('d M Y') }}</span>
                                                    <small class="text-muted">{{ optional($transaction['date'])->format('H:i') }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $transaction['badge'] }}">{{ $transaction['label'] }}</span>
                                                </td>
                                                <td>
                                                    @if($transaction['route'])
                                                        <a href="{{ $transaction['route'] }}">{{ $transaction['reference'] ?? '—' }}</a>
                                                    @else
                                                        {{ $transaction['reference'] ?? '—' }}
                                                    @endif
                                                </td>
                                                <td>{{ $transaction['party'] ?? '—' }}</td>
                                                <td class="text-end">{{ $transaction['quantity'] }}</td>
                                                <td class="text-end">{{ format_currency($transaction['unit_price']) }}</td>
                                                <td class="text-end fw-bold">{{ format_currency($transaction['sub_total']) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                <!-- Product Image -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0"><i class="bi bi-image"></i> Product Image</h5>
                    </div>
                    <div class="card-body text-center">
                        @forelse($product->getMedia('images') as $media)
                            <img src="{{ $media->getUrl() }}" alt="Product Image" class="img-contain-300 img-fluid rounded mb-2">
                        @empty
                            <img src="{{ $product->getFirstMediaUrl('images') }}" alt="Product Image" class="img-contain-300 img-fluid rounded mb-2">
                        @endforelse
                    </div>
                </div>

                <!-- Barcode Section -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0"><i class="bi bi-qr-code"></i> Barcode</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            {!! \Milon\Barcode\Facades\DNS1DFacade::getBarCodeSVG($product->product_code, $product->product_barcode_symbology, 2, 110) !!}
                        </div>
                        <small class="text-muted d-block">{{ $product->product_code }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



