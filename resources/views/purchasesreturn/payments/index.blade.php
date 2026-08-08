@extends('layouts.app')

@section('title', __('purchase-returns.payments'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}"> {{ __('general.home') }} </a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchase-returns.index') }}"> {{ __('purchase-returns.purchase_returns') }} </a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchases.show', $purchase_return) }}">{{ $purchase_return->reference }}</a></li>
        <li class="breadcrumb-item active"> {{ __('purchase-returns.payments') }} </li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        @include('utils.alerts')
        <div class="row">
            @forelse($payments as $payment)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $payment->reference }}</h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.date') }}</span><span>{{ $payment->date }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.amount') }}</span><span>{{ format_currency($payment->amount) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.payment_method') }}</span><span>{{ $payment->payment_method }}</span></li>
                            </ul>
                            <div class="btn-group">
                                @include('purchasesreturn.payments.partials.actions', ['data' => $payment])
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">{{ __('purchase-returns.no_payments_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $payments->links() }}
        </div>
    </div>
@endsection
