@extends('layouts.app')

@section('title', __('sales.details'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('common.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">{{ __('sales.title') }}</a></li>
        <li class="breadcrumb-item active">{{ __('sales.details') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        @if($sale->bonLivraison)
            <x-document-chain current="sale" :quotation="optional(optional($sale->bonLivraison)->commande)->quotation ?? optional(optional(optional($sale->bonLivraison)->commande)->bonCommande)->quotation" :bon-commande="optional(optional($sale->bonLivraison)->commande)->bonCommande" :commande="optional($sale->bonLivraison)->commande" :bon-livraison="$sale->bonLivraison" :sale="$sale" />
        @elseif($sale->commande)
            <x-document-chain current="sale" :quotation="optional(optional($sale->commande->bonCommande))->quotation ?? optional($sale->commande)->quotation" :bon-commande="optional($sale->commande)->bonCommande" :commande="$sale->commande" :sale="$sale" />
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex flex-wrap align-items-center">
                        <div>
                            {{ __('sales.reference') }}: <strong>{{ $sale->reference }}</strong>
                        </div>
                        <a target="_blank" class="btn btn-sm btn-secondary mfs-auto mfe-1 d-print-none" href="{{ route('sales.pdf', $sale->id) }}">
                            <i class="bi bi-printer"></i> {{ __('common.print') }}
                        </a>
                        <a target="_blank" class="btn btn-sm btn-info mfe-1 d-print-none" href="{{ route('sales.pdf', $sale->id) }}">
                            <i class="bi bi-save"></i> {{ __('common.save') }}
                        </a>
                    </div>
                    <div class="flex-auto p-2">
                        <div class="row mb-4">
                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('sales.company_info') }}:</h5>
                                <div><strong>{{ settings()->company_name }}</strong></div>
                                <div>{{ settings()->company_address }}</div>
                                <div>{{ __('common.email') }}: {{ settings()->company_email }}</div>
                                <div>{{ __('common.phone') }}: {{ settings()->company_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('sales.customer_info') }}:</h5>
                                <div><strong>{{ $customer->customer_name }}</strong></div>
                                <div>{{ $customer->address }}</div>
                                <div>{{ __('common.email') }}: {{ $customer->customer_email }}</div>
                                <div>{{ __('common.phone') }}: {{ $customer->customer_phone }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">{{ __('sales.invoice_info') }}:</h5>
                                <div>{{ __('sales.invoice') }}: <strong>INV/{{ $sale->reference }}</strong></div>
                                <div>{{ __('common.date') }}: {{ \Carbon\Carbon::parse($sale->date)->format('d M, Y') }}</div>
                                <div>
                                    {{ __('sales.status') }}: <strong>{{ $sale->status }}</strong>
                                </div>
                                <div>
                                    {{ __('sales.payment_status') }}: <strong>{{ $sale->payment_status }}</strong>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            @foreach($sale->saleDetails as $item)
                                <div class="col-xl-4 col-lg-6 mb-4">
                                    <div class="card border h-100">
                                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                                            {{ $item->product_name }}
                                            <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">{{ $item->product_code }}</span>
                                        </div>
                                        <div class="flex-auto p-2">
                                            <ul class="list-group list-group-flush mb-0">
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.net_unit_price') }}</span><span>{{ format_currency($item->unit_price) }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.quantity') }}</span><span>{{ $item->quantity }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.discount') }}</span><span>{{ format_currency($item->product_discount_amount) }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.tax') }}</span><span>{{ format_currency($item->product_tax_amount) }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.sub_total') }}</span><span class="fw-bold">{{ format_currency($item->sub_total) }}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-sm-5 ml-md-auto">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between"><strong>{{ __('sales.discount') }}    ({{ $sale->discount_percentage }}%)</strong><span>{{ format_currency($sale->discount_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between"><strong>{{ __('sales.tax') }} ({{ $sale->tax_percentage }}%)</strong><span>{{ format_currency($sale->tax_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between"><strong>{{ __('sales.shipping') }}</strong><span>{{ format_currency($sale->shipping_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between"><strong>{{ __('sales.grand_total') }}</strong><strong>{{ format_currency($sale->total_amount) }}</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

