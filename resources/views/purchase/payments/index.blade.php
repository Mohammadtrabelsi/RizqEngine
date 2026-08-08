@extends('layouts.app')

@section('title', __('purchase.payments'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('purchase.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">{{ __('purchase.purchases') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchases.show', $purchase) }}">{{ $purchase->reference }}</a></li>
        <li class="breadcrumb-item active">{{ __('purchase.payments') }}</li>
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
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase.date') }}</span><span>{{ $payment->date }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase.amount') }}</span><span>{{ format_currency($payment->amount) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase.method') }}</span><span>{{ $payment->payment_method }}</span></li>
                            </ul>
                            <div class="btn-group">
                                @include('purchase.payments.partials.actions', ['data' => $payment])
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">{{ __('purchase.no_payments_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $payments->links() }}
        </div>
    </div>
@endsection
