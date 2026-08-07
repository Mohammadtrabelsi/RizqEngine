@extends('layouts.app')

@section('title', 'Currencies')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Currencies</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('currencies.create') }}" class="btn btn-primary">
                    Add Currency <i class="bi bi-plus"></i>
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
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Symbol</span><span>{{ $currency->symbol }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Thousand Sep.</span><span>{{ $currency->thousand_separator }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Decimal Sep.</span><span>{{ $currency->decimal_separator }}</span></li>
                            </ul>
                            <div class="btn-group">
                                @include('currency::partials.actions', ['data' => $currency])
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">No currencies found.</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $currencies->links() }}
        </div>
    </div>
@endsection
