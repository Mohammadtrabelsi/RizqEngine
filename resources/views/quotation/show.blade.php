@extends('layouts.app')

@section('title', __('quotations.quotation_details'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('common.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">{{ __('quotations.quotations') }}</a></li>
        <li class="breadcrumb-item active">{{ __('quotations.details') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center">
                        <div>
                            Reference: <strong>{{ $quotation->reference }}</strong>
                        </div>
                        <a target="_blank" class="btn btn-sm btn-secondary mfs-auto mfe-1 d-print-none" href="{{ route('quotations.pdf', $quotation->id) }}">
                            <i class="bi bi-printer"></i> {{ __('quotations.print') }}
                        </a>
                        <a target="_blank" class="btn btn-sm btn-info mfe-1 d-print-none" href="{{ route('quotations.pdf', $quotation->id) }}">
                            <i class="bi bi-save"></i> {{ __('quotations.save') }}
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('quotations.company_info') }}</h5>
                                <div><strong>{{ settings()->company_name }}</strong></div>
                                <div>{{ settings()->company_address }}</div>
                                <div>{{ __('quotations.email') }}: {{ settings()->company_email }}</div>
                                <div>{{ __('quotations.phone') }}: {{ settings()->company_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('quotations.customer_info') }}</h5>
                                <div><strong>{{ $customer->customer_name }}</strong></div>
                                <div>{{ $customer->address }}</div>
                                <div>{{ __('quotations.email') }}: {{ $customer->customer_email }}</div>
                                <div>{{ __('quotations.phone') }}: {{ $customer->customer_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('quotations.invoice_info') }}</h5>
                                <div>{{ __('quotations.invoice') }}: <strong>INV/{{ $quotation->reference }}</strong></div>
                                <div>{{ __('quotations.date') }}: {{ \Carbon\Carbon::parse($quotation->date)->format('d M, Y') }}</div>
                                <div>
                                    {{ __('quotations.status') }}: <strong>{{ $quotation->status }}</strong>
                                </div>
                                <div>
                                    {{ __('quotations.payment_status') }}: <strong>{{ $quotation->payment_status }}</strong>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            @foreach($quotation->quotationDetails as $item)
                                <div class="col-xl-4 col-lg-6 mb-4">
                                    <div class="card border h-100">
                                        <div class="card-header">
                                            {{ $item->product_name }}
                                            <span class="badge badge-success">{{ $item->product_code }}</span>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush mb-0">
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('quotations.net_unit_price') }}</span><span>{{ format_currency($item->unit_price) }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('quotations.quantity') }}</span><span>{{ $item->quantity }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('quotations.discount') }}</span><span>{{ format_currency($item->product_discount_amount) }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('quotations.tax') }}</span><span>{{ format_currency($item->product_tax_amount) }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('quotations.sub_total') }}</span><span class="fw-bold">{{ format_currency($item->sub_total) }}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-sm-5 ml-md-auto">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between"><strong>{{ __('quotations.discount') }} ({{ $quotation->discount_percentage }}%)</strong><span>{{ format_currency($quotation->discount_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between"><strong>{{ __('quotations.tax') }} ({{ $quotation->tax_percentage }}%)</strong><span>{{ format_currency($quotation->tax_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between"><strong>{{ __('quotations.shipping') }}</strong><span>{{ format_currency($quotation->shipping_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between"><strong>{{ __('quotations.grand_total') }}</strong><strong>{{ format_currency($quotation->total_amount) }}</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

