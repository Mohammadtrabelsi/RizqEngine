@extends('layouts.app')

@section('title', __('currency.currencies'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('currency.currencies') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('currencies.create') }}" class="btn btn-primary">
                    {{ __('currency.add_currency') }} <i class="bi bi-plus"></i>
                </a>
            </div>
        </div>

        <div class="row">
            @forelse($currencies as $currency)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $currency->currency_name }} <small class="text-muted">({{ $currency->code }})</small></h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('currency.symbol') }}</span><span>{{ $currency->symbol }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('currency.thousand_separator') }}</span><span>{{ $currency->thousand_separator }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('currency.decimal_separator') }}</span><span>{{ $currency->decimal_separator }}</span></li>
                            </ul>
                            <div class="btn-group">
                                @include('currency::partials.actions', ['data' => $currency])
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">{{ __('currency.no_currencies_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $currencies->links() }}
        </div>
    </div>
@endsection
