@extends('layouts.app')

@section('title', 'Purchases Details')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Purchases</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center">
                        <div>
                            Reference: <strong>{{ $purchase->reference }}</strong>
                        </div>
                        <a target="_blank" class="btn btn-sm btn-secondary mfs-auto mfe-1 d-print-none" href="{{ route('purchases.pdf', $purchase->id) }}">
                            <i class="bi bi-printer"></i> Print
                        </a>
                        <a target="_blank" class="btn btn-sm btn-info mfe-1 d-print-none" href="{{ route('purchases.pdf', $purchase->id) }}">
                            <i class="bi bi-save"></i> Save
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">Company Info:</h5>
                                <div><strong>{{ settings()->company_name }}</strong></div>
                                <div>{{ settings()->company_address }}</div>
                                <div>Email: {{ settings()->company_email }}</div>
                                <div>Phone: {{ settings()->company_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">Supplier Info:</h5>
                                <div><strong>{{ $supplier->supplier_name }}</strong></div>
                                <div>{{ $supplier->address }}</div>
                                <div>Email: {{ $supplier->supplier_email }}</div>
                                <div>Phone: {{ $supplier->supplier_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">Invoice Info:</h5>
                                <div>Invoice: <strong>INV/{{ $purchase->reference }}</strong></div>
                                <div>Date: {{ \Carbon\Carbon::parse($purchase->date)->format('d M, Y') }}</div>
                                <div>
                                    Status: <strong>{{ $purchase->status }}</strong>
                                </div>
                                <div>
                                    Payment Status: <strong>{{ $purchase->payment_status }}</strong>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            @foreach($purchase->purchaseDetails as $item)
                                <div class="col-xl-4 col-lg-6 mb-4">
                                    <div class="card border h-100">
                                        <div class="card-header">
                                            {{ $item->product_name }}
                                            <span class="badge badge-success">{{ $item->product_code }}</span>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush mb-0">
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>Net Unit Price</span><span>{{ format_currency($item->unit_price) }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>Quantity</span><span>{{ $item->quantity }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>Discount</span><span>{{ format_currency($item->product_discount_amount) }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>Tax</span><span>{{ format_currency($item->product_tax_amount) }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>Sub Total</span><span class="fw-bold">{{ format_currency($item->sub_total) }}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-sm-5 ml-md-auto">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between"><strong>Discount ({{ $purchase->discount_percentage }}%)</strong><span>{{ format_currency($purchase->discount_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between"><strong>Tax ({{ $purchase->tax_percentage }}%)</strong><span>{{ format_currency($purchase->tax_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between"><strong>Shipping</strong><span>{{ format_currency($purchase->shipping_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between"><strong>Grand Total</strong><strong>{{ format_currency($purchase->total_amount) }}</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

