@extends('layouts.app')

@section('title', __('bonlivraison.details'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('bon-livraisons.index') }}">{{ __('bonlivraison.bon_livraisons') }}</a></li>
        <li class="breadcrumb-item active">{{ $bonLivraison->reference }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        @include('utils.alerts')

        <x-document-chain
            current="bon_livraison"
            :quotation="optional($bonLivraison->commande)->quotation ?? optional(optional($bonLivraison->commande)->bonCommande)->quotation"
            :commande="$bonLivraison->commande"
            :bon-livraison="$bonLivraison"
            :sale="$bonLivraison->sale" />

        <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex flex-wrap align-items-center">
                <div>
                    {{ __('bonlivraison.reference') }}: <strong>{{ $bonLivraison->reference }}</strong>
                    @include('bonlivraison.partials.status', ['data' => $bonLivraison])
                </div>
                <div class="mfs-auto d-print-none">
                    @if($bonLivraison->status === \App\Models\BonLivraison::STATUS_PENDING)
                        @can('deliver_bon_livraisons')
                            <form class="d-inline" action="{{ route('bon-livraisons.deliver', $bonLivraison->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !px-3 !py-1.5 !text-xs bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700"><i class="bi bi-truck"></i> {{ __('bonlivraison.mark-delivered') }}</button>
                            </form>
                        @endcan
                    @endif
                    @if(! $bonLivraison->isInvoiced())
                        @can('convert_bon_livraisons')
                            <form class="d-inline" action="{{ route('bon-livraisons.convert', $bonLivraison->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !px-3 !py-1.5 !text-xs bg-emerald-500 !text-white border-emerald-500 hover:bg-emerald-600 hover:border-emerald-600"><i class="bi bi-receipt"></i> {{ __('bonlivraison.generate-facture') }}</button>
                            </form>
                        @endcan
                    @endif
                </div>
            </div>
            <div class="flex-auto p-5">
                <div class="row mb-4">
                    <div class="col-sm-4 mb-3 mb-md-0">
                        <h5 class="mb-2 border-bottom pb-2">{{ __('bonlivraison.company_info') }}</h5>
                        <div><strong>{{ settings()->company_name }}</strong></div>
                        <div>{{ settings()->company_address }}</div>
                        <div>{{ __('bonlivraison.email') }}: {{ settings()->company_email }}</div>
                        <div>{{ __('bonlivraison.phone') }}: {{ settings()->company_phone }}</div>
                    </div>
                    <div class="col-sm-4 mb-3 mb-md-0">
                        <h5 class="mb-2 border-bottom pb-2">{{ __('bonlivraison.customer_info') }}</h5>
                        <div><strong>{{ $customer->customer_name }}</strong></div>
                        <div>{{ $customer->address }}</div>
                        <div>{{ __('bonlivraison.email') }}: {{ $customer->customer_email }}</div>
                        <div>{{ __('bonlivraison.phone') }}: {{ $customer->customer_phone }}</div>
                    </div>
                    <div class="col-sm-4 mb-3 mb-md-0">
                        <h5 class="mb-2 border-bottom pb-2">{{ __('bonlivraison.delivery_info') }}</h5>
                        <div>{{ __('bonlivraison.reference') }}: <strong>{{ $bonLivraison->reference }}</strong></div>
                        <div>{{ __('bonlivraison.date') }}: {{ $bonLivraison->date }}</div>
                        <div>{{ __('bonlivraison.status_label') }}: <strong>{{ __('bonlivraison.status_'.$bonLivraison->status) }}</strong></div>
                    </div>
                </div>

                <div class="block w-full overflow-x-auto">
                    <table class="w-full mb-4 text-slate-900 border-collapse table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('bonlivraison.product') }}</th>
                                <th>{{ __('bonlivraison.code') }}</th>
                                <th class="text-end">{{ __('bonlivraison.quantity') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bonLivraison->bonLivraisonDetails as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td><span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">{{ $item->product_code }}</span></td>
                                    <td class="text-end fw-bold">{{ $item->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($bonLivraison->note)
                    <p class="mt-3"><span class="fw-bold">{{ __('bonlivraison.note') }}:</span> {{ $bonLivraison->note }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection
