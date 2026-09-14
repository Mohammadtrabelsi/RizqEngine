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
                                    @foreach($sale->saleDetails as $item)
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
                                    <li class="list-group-item d-flex justify-content-between"><strong>{{ __('sales.discount') }}    ({{ $sale->discount_percentage }}%)</strong><span>{{ format_currency($sale->discount_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between"><strong>{{ __('sales.tax') }} ({{ $sale->tax_percentage }}%)</strong><span>{{ format_currency($sale->tax_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between"><strong>{{ __('sales.shipping') }}</strong><span>{{ format_currency($sale->shipping_amount) }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between bg-slate-50"><strong>{{ __('sales.grand_total') }}</strong><strong>{{ format_currency($sale->total_amount) }}</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

