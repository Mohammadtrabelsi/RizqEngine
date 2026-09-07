@extends('layouts.app')

@section('title', __('commande.details'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('commandes.index') }}">{{ __('commande.commandes') }}</a></li>
        <li class="breadcrumb-item active">{{ $commande->reference }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        @include('utils.alerts')

        @if($commande->bonLivraison)
            <x-document-chain current="commande" :quotation="$commande->quotation ?? optional($commande->bonCommande)->quotation" :commande="$commande" :bon-livraison="$commande->bonLivraison" :sale="optional($commande->bonLivraison)->sale ?? $commande->sale" />
        @else
            <x-document-chain current="commande" :quotation="$commande->quotation ?? optional($commande->bonCommande)->quotation" :bon-commande="$commande->bonCommande" :commande="$commande" :sale="$commande->sale" />
        @endif

        <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex flex-wrap align-items-center">
                <div>
                    {{ __('commande.reference') }}: <strong>{{ $commande->reference }}</strong>
                    @include('commande.partials.status', ['data' => $commande])
                </div>
                <div class="mfs-auto d-print-none">
                    @if($commande->status === \App\Models\Commande::STATUS_PENDING)
                        @can('confirm_commandes')
                            <form class="d-inline" action="{{ route('commandes.confirm', $commande->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !px-3 !py-1.5 !text-xs bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700"><i class="bi bi-check2-circle"></i> {{ __('commande.confirm') }}</button>
                            </form>
                        @endcan
                    @endif
                    @if(! $commande->hasBonLivraison())
                        @can('convert_commandes_to_bon_livraison')
                            <form class="d-inline" action="{{ route('commandes.convert-bon-livraison', $commande->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !px-3 !py-1.5 !text-xs bg-cyan-500 !text-white border-cyan-500 hover:bg-cyan-600"><i class="bi bi-truck"></i> {{ __('commande.create-bon-livraison') }}</button>
                            </form>
                        @endcan
                    @endif
                    @if(! $commande->hasStockExit())
                        @can('convert_commandes_to_stock_exit')
                            <form class="d-inline" action="{{ route('commandes.convert-stock-exit', $commande->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !px-3 !py-1.5 !text-xs bg-amber-500 !text-white border-amber-500 hover:bg-amber-600 hover:border-amber-600"><i class="bi bi-box-arrow-up"></i> {{ __('commande.create-stock-exit') }}</button>
                            </form>
                        @endcan
                    @endif
                    @if(! $commande->isInvoiced())
                        @can('convert_commandes')
                            <form class="d-inline" action="{{ route('commandes.convert', $commande->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !px-3 !py-1.5 !text-xs bg-emerald-500 !text-white border-emerald-500 hover:bg-emerald-600 hover:border-emerald-600"><i class="bi bi-receipt"></i> {{ __('commande.generate-facture') }}</button>
                            </form>
                        @endcan
                    @endif
                </div>
            </div>
            <div class="flex-auto p-5">
                <div class="row mb-4">
                    <div class="col-sm-4 mb-3 mb-md-0">
                        <h5 class="mb-2 border-bottom pb-2">{{ __('commande.company_info') }}</h5>
                        <div><strong>{{ settings()->company_name }}</strong></div>
                        <div>{{ settings()->company_address }}</div>
                        <div>{{ __('commande.email') }}: {{ settings()->company_email }}</div>
                        <div>{{ __('commande.phone') }}: {{ settings()->company_phone }}</div>
                    </div>
                    <div class="col-sm-4 mb-3 mb-md-0">
                        <h5 class="mb-2 border-bottom pb-2">{{ __('commande.customer_info') }}</h5>
                        <div><strong>{{ $customer->customer_name }}</strong></div>
                        <div>{{ $customer->address }}</div>
                        <div>{{ __('commande.email') }}: {{ $customer->customer_email }}</div>
                        <div>{{ __('commande.phone') }}: {{ $customer->customer_phone }}</div>
                    </div>
                    <div class="col-sm-4 mb-3 mb-md-0">
                        <h5 class="mb-2 border-bottom pb-2">{{ __('commande.order_info') }}</h5>
                        <div>{{ __('commande.reference') }}: <strong>{{ $commande->reference }}</strong></div>
                        <div>{{ __('commande.date') }}: {{ $commande->date }}</div>
                        <div>{{ __('commande.status_label') }}: <strong>{{ __('commande.status_'.$commande->status) }}</strong></div>
                    </div>
                </div>

                <div class="row">
                    @foreach($commande->commandeDetails as $item)
                        <div class="col-xl-4 col-lg-6 mb-4">
                            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border h-100">
                                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                                    {{ $item->product_name }}
                                    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">{{ $item->product_code }}</span>
                                </div>
                                <div class="flex-auto p-5">
                                    <ul class="list-group list-group-flush mb-0">
                                        <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('commande.net_unit_price') }}</span><span>{{ format_currency($item->unit_price) }}</span></li>
                                        <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('commande.quantity') }}</span><span>{{ $item->quantity }}</span></li>
                                        <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('commande.discount') }}</span><span>{{ format_currency($item->product_discount_amount) }}</span></li>
                                        <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('commande.tax') }}</span><span>{{ format_currency($item->product_tax_amount) }}</span></li>
                                        <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('commande.sub_total') }}</span><span class="fw-bold">{{ format_currency($item->sub_total) }}</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="row">
                    <div class="col-lg-4 col-sm-5 ml-md-auto">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between"><strong>{{ __('commande.discount') }} ({{ $commande->discount_percentage }}%)</strong><span>{{ format_currency($commande->discount_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>{{ __('commande.tax') }} ({{ $commande->tax_percentage }}%)</strong><span>{{ format_currency($commande->tax_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>{{ __('commande.shipping') }}</strong><span>{{ format_currency($commande->shipping_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>{{ __('commande.grand_total') }}</strong><strong>{{ format_currency($commande->total_amount) }}</strong></li>
                        </ul>
                    </div>
                </div>

                @if($commande->note)
                    <p class="mt-3"><span class="fw-bold">{{ __('commande.note') }}:</span> {{ $commande->note }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection
