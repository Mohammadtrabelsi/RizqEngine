@extends('layouts.app')

@section('title',   __('purchase-returns.purchase_returns'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('purchase-returns.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchase-returns.index') }}">{{ __('purchase-returns.purchase_returns') }}</a></li>
        <li class="breadcrumb-item active">{{ __('purchase-returns.details') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex flex-wrap align-items-center">
                        <div>
                            {{ __('purchase-returns.reference') }}: <strong>{{ $purchase_return->reference }}</strong>
                        </div>
                        <a target="_blank" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !px-3 !py-1.5 !text-xs bg-white !text-slate-700 border-slate-300 hover:bg-slate-50 hover:!text-slate-900 hover:border-slate-400 mfs-auto mfe-1 d-print-none" href="{{ route('purchase-returns.pdf', $purchase_return->id) }}">
                            <i class="bi bi-printer"></i> {{ __('purchase-returns.print') }}
                        </a>
                        <a target="_blank" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !px-3 !py-1.5 !text-xs bg-cyan-500 !text-white border-cyan-500 hover:bg-cyan-600 mfe-1 d-print-none" href="{{ route('purchase-returns.pdf', $purchase_return->id) }}">
                            <i class="bi bi-save"></i> {{ __('purchase-returns.save') }}
                        </a>
                    </div>
                    <div class="flex-auto p-2">
                        <div class="row mb-4">
                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('purchase-returns.company_info') }}:</h5>
                                <div><strong>{{ settings()->company_name }}</strong></div>
                                <div>{{ settings()->company_address }}</div>
                                <div>{{ __('purchase-returns.email') }}: {{ settings()->company_email }}</div>
                                <div>{{ __('purchase-returns.phone') }}: {{ settings()->company_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('purchase-returns.supplier_info') }}:</h5>
                                <div><strong>{{ $supplier->supplier_name }}</strong></div>
                                <div>{{ $supplier->address }}</div>
                                <div>{{ __('purchase-returns.email') }}: {{ $supplier->supplier_email }}</div>
                                <div>{{ __('purchase-returns.phone') }}: {{ $supplier->supplier_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('purchase-returns.invoice_info') }}:</h5>
                                <div>{{ __('purchase-returns.invoice') }}: <strong>INV/{{ $purchase_return->reference }}</strong></div>
                                <div>{{ __('purchase-returns.date') }}: {{ \Carbon\Carbon::parse($purchase_return->date)->format('d M, Y') }}</div>
                                <div>
                                    {{ __('purchase-returns.status') }}: <strong>{{ $purchase_return->status }}</strong>
                                </div>
                                <div>
                                    {{ __('purchase-returns.payment_status') }}: <strong>{{ $purchase_return->payment_status }}</strong>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            @foreach($purchase_return->purchaseReturnDetails as $item)
                                <div class="col-xl-4 col-lg-6 mb-4">
                                    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border h-100">
                                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                                            {{ $item->product_name }}
                                            <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">{{ $item->product_code }}</span>
                                        </div>
                                        <div class="flex-auto p-2">
                                            <ul class="list-group list-group-flush mb-0">
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.net_unit_price') }}</span><span>{{ format_currency($item->unit_price) }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.quantity') }}</span><span>{{ $item->quantity }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.discount') }}</span><span>{{ format_currency($item->product_discount_amount) }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.tax') }}</span><span>{{ format_currency($item->product_tax_amount) }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.sub_total') }}</span><span class="fw-bold">{{ format_currency($item->sub_total) }}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-sm-5 ml-md-auto">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between"><strong>{{ __('purchase-returns.discount') }} ({{ $purchase_return->discount_percentage }}%)</strong><span>{{ format_currency($purchase_return->discount_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between"><strong>{{ __('purchase-returns.tax') }} ({{ $purchase_return->tax_percentage }}%)</strong><span>{{ format_currency($purchase_return->tax_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between"><strong>{{ __('purchase-returns.shipping') }}</strong><span>{{ format_currency($purchase_return->shipping_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between"><strong>{{ __('purchase-returns.grand_total') }}</strong><strong>{{ format_currency($purchase_return->total_amount) }}</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

