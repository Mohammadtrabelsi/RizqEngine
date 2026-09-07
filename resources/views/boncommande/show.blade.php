@extends('layouts.app')

@section('title', __('boncommande.details'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('bon-commandes.index') }}">{{ __('boncommande.bon_commandes') }}</a></li>
        <li class="breadcrumb-item active">{{ $bonCommande->reference }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        @include('utils.alerts')

        <x-document-chain current="bon_commande" :quotation="$bonCommande->quotation" :bon-commande="$bonCommande" :commande="$bonCommande->commande" :sale="optional($bonCommande->commande)->sale" />

        <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex flex-wrap align-items-center">
                <div>
                    {{ __('boncommande.reference') }}: <strong>{{ $bonCommande->reference }}</strong>
                    @include('boncommande.partials.status', ['data' => $bonCommande])
                </div>
                <div class="mfs-auto d-print-none">
                    @if($bonCommande->status === \App\Models\BonCommande::STATUS_DRAFT)
                        @can('edit_bon_commandes')
                            <a class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !px-3 !py-1.5 !text-xs bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700" href="{{ route('bon-commandes.edit', $bonCommande->id) }}">
                                <i class="bi bi-pencil"></i> {{ __('boncommande.edit') }}
                            </a>
                        @endcan
                        @can('confirm_bon_commandes')
                            <form class="d-inline" action="{{ route('bon-commandes.confirm', $bonCommande->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !px-3 !py-1.5 !text-xs bg-emerald-500 !text-white border-emerald-500 hover:bg-emerald-600 hover:border-emerald-600"><i class="bi bi-check2-circle"></i> {{ __('boncommande.confirm') }}</button>
                            </form>
                        @endcan
                    @endif
                    @if($bonCommande->status === \App\Models\BonCommande::STATUS_CONFIRMED)
                        @can('convert_bon_commandes')
                            <form class="d-inline" action="{{ route('bon-commandes.convert', $bonCommande->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !px-3 !py-1.5 !text-xs bg-emerald-500 !text-white border-emerald-500 hover:bg-emerald-600 hover:border-emerald-600"><i class="bi bi-arrow-right-circle"></i> {{ __('boncommande.convert-to-commande') }}</button>
                            </form>
                        @endcan
                    @endif
                    @if(! $bonCommande->isConverted() && ! $bonCommande->isCancelled())
                        @can('edit_bon_commandes')
                            <form class="d-inline" action="{{ route('bon-commandes.cancel', $bonCommande->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !px-3 !py-1.5 !text-xs bg-amber-500 !text-white border-amber-500 hover:bg-amber-600"><i class="bi bi-x-circle"></i> {{ __('boncommande.cancel') }}</button>
                            </form>
                        @endcan
                    @endif
                </div>
            </div>
            <div class="flex-auto p-5">
                <div class="row mb-4">
                    <div class="col-sm-4 mb-3 mb-md-0">
                        <h5 class="mb-2 border-bottom pb-2">{{ __('boncommande.company_info') }}</h5>
                        <div><strong>{{ settings()->company_name }}</strong></div>
                        <div>{{ settings()->company_address }}</div>
                        <div>{{ __('boncommande.email') }}: {{ settings()->company_email }}</div>
                        <div>{{ __('boncommande.phone') }}: {{ settings()->company_phone }}</div>
                    </div>
                    <div class="col-sm-4 mb-3 mb-md-0">
                        <h5 class="mb-2 border-bottom pb-2">{{ __('boncommande.customer_info') }}</h5>
                        <div><strong>{{ $customer->customer_name }}</strong></div>
                        <div>{{ $customer->address }}</div>
                        <div>{{ __('boncommande.email') }}: {{ $customer->customer_email }}</div>
                        <div>{{ __('boncommande.phone') }}: {{ $customer->customer_phone }}</div>
                    </div>
                    <div class="col-sm-4 mb-3 mb-md-0">
                        <h5 class="mb-2 border-bottom pb-2">{{ __('boncommande.order_info') }}</h5>
                        <div>{{ __('boncommande.reference') }}: <strong>{{ $bonCommande->reference }}</strong></div>
                        <div>{{ __('boncommande.date') }}: {{ $bonCommande->date }}</div>
                        <div>{{ __('boncommande.status_label') }}: <strong>{{ __('boncommande.status_'.$bonCommande->status) }}</strong></div>
                    </div>
                </div>

                <div class="row">
                    @foreach($bonCommande->bonCommandeDetails as $item)
                        <div class="col-xl-4 col-lg-6 mb-4">
                            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border h-100">
                                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                                    {{ $item->product_name }}
                                    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">{{ $item->product_code }}</span>
                                </div>
                                <div class="flex-auto p-5">
                                    <ul class="list-group list-group-flush mb-0">
                                        <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('boncommande.net_unit_price') }}</span><span>{{ format_currency($item->unit_price) }}</span></li>
                                        <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('boncommande.quantity') }}</span><span>{{ $item->quantity }}</span></li>
                                        <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('boncommande.discount') }}</span><span>{{ format_currency($item->product_discount_amount) }}</span></li>
                                        <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('boncommande.tax') }}</span><span>{{ format_currency($item->product_tax_amount) }}</span></li>
                                        <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('boncommande.sub_total') }}</span><span class="fw-bold">{{ format_currency($item->sub_total) }}</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="row">
                    <div class="col-lg-4 col-sm-5 ml-md-auto">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between"><strong>{{ __('boncommande.discount') }} ({{ $bonCommande->discount_percentage }}%)</strong><span>{{ format_currency($bonCommande->discount_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>{{ __('boncommande.tax') }} ({{ $bonCommande->tax_percentage }}%)</strong><span>{{ format_currency($bonCommande->tax_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>{{ __('boncommande.shipping') }}</strong><span>{{ format_currency($bonCommande->shipping_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>{{ __('boncommande.grand_total') }}</strong><strong>{{ format_currency($bonCommande->total_amount) }}</strong></li>
                        </ul>
                    </div>
                </div>

                @if($bonCommande->note)
                    <p class="mt-3"><span class="fw-bold">{{ __('boncommande.note') }}:</span> {{ $bonCommande->note }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection
