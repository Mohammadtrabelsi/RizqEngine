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

        @include('partials.document-chain', [
            'current' => 'commande',
            'quotation' => optional($commande->bonCommande)->quotation,
            'bonCommande' => $commande->bonCommande,
            'commande' => $commande,
            'sale' => $commande->sale,
        ])

        <div class="card">
            <div class="card-header d-flex flex-wrap align-items-center">
                <div>
                    {{ __('commande.reference') }}: <strong>{{ $commande->reference }}</strong>
                    @include('commande.partials.status', ['data' => $commande])
                </div>
                <div class="mfs-auto d-print-none">
                    @if($commande->status === \App\Models\Commande::STATUS_PENDING)
                        @can('confirm_commandes')
                            <form class="d-inline" action="{{ route('commandes.confirm', $commande->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check2-circle"></i> {{ __('commande.confirm') }}</button>
                            </form>
                        @endcan
                    @endif
                    @if(! $commande->isInvoiced())
                        @can('convert_commandes')
                            <form class="d-inline" action="{{ route('commandes.convert', $commande->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-receipt"></i> {{ __('commande.generate-facture') }}</button>
                            </form>
                        @endcan
                    @endif
                </div>
            </div>
            <div class="card-body">
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
                            <div class="card border h-100">
                                <div class="card-header">
                                    {{ $item->product_name }}
                                    <span class="badge badge-success">{{ $item->product_code }}</span>
                                </div>
                                <div class="card-body">
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
