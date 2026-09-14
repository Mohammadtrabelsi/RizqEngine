@extends('layouts.app')

@section('title', __('purchase.purchase_details'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('purchase.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">{{ __('purchase.purchases') }}</a></li>
        <li class="breadcrumb-item active">{{ __('purchase.details') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex flex-wrap align-items-center">
                        <div>
                            Reference: <strong>{{ $purchase->reference }}</strong>
                        </div>
                        <a target="_blank" class="btn btn-sm btn-secondary mfs-auto mfe-1 d-print-none" href="{{ route('purchases.pdf', $purchase->id) }}">
                            <i class="bi bi-printer"></i> Print
                        </a>
                        <a target="_blank" class="btn btn-sm btn-info mfe-1 d-print-none" href="{{ route('purchases.pdf', $purchase->id) }}">
                            <i class="bi bi-save"></i> Save
                        </a>
                        @if($purchase->withholding_amount > 0)
                            <a target="_blank" class="btn btn-sm btn-warning mfe-1 d-print-none" href="{{ route('purchases.withholding-certificate', $purchase->id) }}">
                                <i class="bi bi-file-earmark-text"></i> {{ __('withholding.certificate') }}
                            </a>
                        @endif
                    </div>
                    <div class="flex-auto p-2">
                        <div class="row mb-4">
                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('purchase.company_info') }}</h5>
                                <div><strong>{{ settings()->company_name }}</strong></div>
                                <div>{{ settings()->company_address }}</div>
                                <div>Email: {{ settings()->company_email }}</div>
                                <div>Phone: {{ settings()->company_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('purchase.supplier_info') }}</h5>
                                <div><strong>{{ $supplier->supplier_name }}</strong></div>
                                <div>{{ $supplier->address }}</div>
                                <div>Email: {{ $supplier->supplier_email }}</div>
                                <div>Phone: {{ $supplier->supplier_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('purchase.invoice_info') }}</h5>
                                <div>{{ __('purchase.invoice_number') }}: <strong>INV/{{ $purchase->reference }}</strong></div>
                                <div>{{ __('purchase.date') }}: {{ \Carbon\Carbon::parse($purchase->date)->format('d M, Y') }}</div>
                                @if($purchase->warehouse)
                                    <div>{{ __('warehouses.warehouse') }}: <strong>{{ $purchase->warehouse->name }}</strong></div>
                                @endif
                                <div>
                                    {{ __('purchase.status') }}: <strong>{{ $purchase->status }}</strong>
                                </div>
                                <div>
                                    {{ __('purchase.payment_status') }}: <strong>{{ $purchase->payment_status }}</strong>
                                </div>
                            </div>

                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="text-center" style="width:48px;">#</th>
                                        <th>{{ __('general.product') }}</th>
                                        <th class="text-end">{{ __('sales.net_unit_price') }}</th>
                                        <th class="text-center">{{ __('sales.quantity') }}</th>
                                        <th class="text-end">{{ __('sales.discount') }}</th>
                                        <th class="text-end">{{ __('sales.tax') }}</th>
                                        <th class="text-end">{{ __('sales.sub_total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchase->purchaseDetails as $item)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $item->product_name }}</div>
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">{{ $item->product_code }}</span>
                                            </td>
                                            <td class="text-end">{{ format_currency($item->unit_price) }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-end">{{ format_currency($item->product_discount_amount) }}</td>
                                            <td class="text-end">{{ format_currency($item->product_tax_amount) }}</td>
                                            <td class="text-end fw-bold">{{ format_currency($item->sub_total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-sm-6 ml-md-auto">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between"><strong>Discount ({{ $purchase->discount_percentage }}%)</strong><span>{{ format_currency($purchase->discount_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between"><strong>Tax ({{ $purchase->tax_percentage }}%)</strong><span>{{ format_currency($purchase->tax_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between"><strong>Shipping</strong><span>{{ format_currency($purchase->shipping_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between bg-slate-50"><strong>{{ __('withholding.total_ttc') }}</strong><strong>{{ format_currency($purchase->total_amount) }}</strong></li>
                                    @if($purchase->withholding_amount > 0)
                                        @foreach($purchase->withholdingTaxes as $line)
                                            <li class="list-group-item d-flex justify-content-between"><span>{{ $line->name }} ({{ rtrim(rtrim(number_format($line->rate, 3), '0'), '.') }}%)</span><span>(-) {{ format_currency($line->amount) }}</span></li>
                                        @endforeach
                                        <li class="list-group-item d-flex justify-content-between"><strong>{{ __('withholding.net_payable') }}</strong><strong>{{ format_currency($purchase->net_payable) }}</strong></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

