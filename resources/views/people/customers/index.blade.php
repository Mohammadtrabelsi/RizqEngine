@extends('layouts.app')

@section('title', __('customer.customers'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('customer.customers') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    {{ __('customer.add_customer') }} <i class="bi bi-plus"></i>
                </a>
            </div>
        </div>

        <div class="row">
            @forelse($customers as $customer)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $customer->customer_name }}</h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item px-0"><i class="bi bi-envelope"></i> {{ $customer->customer_email }}</li>
                                <li class="list-group-item px-0"><i class="bi bi-telephone"></i> {{ $customer->customer_phone }}</li>
                            </ul>
                            <div class="btn-group">
                                @include('people.customers.partials.actions', ['data' => $customer])
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">{{ __('customer.no_customers_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $customers->links() }}
        </div>
    </div>
@endsection
