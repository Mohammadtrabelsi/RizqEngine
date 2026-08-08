@extends('layouts.app')

@section('title', {{ __('customer.customer_details') }})

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">{{ __('customer.customers') }}</a></li>
        <li class="breadcrumb-item active">{{ __('customer.customer_details') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.customer_name') }}</span><span>{{ $customer->customer_name }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.customer_email') }}</span><span>{{ $customer->customer_email }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.customer_phone') }}</span><span>{{ $customer->customer_phone }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.city') }}</span><span>{{ $customer->city }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.country') }}</span><span>{{ $customer->country }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.address') }}</span><span>{{ $customer->address }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

