@extends('layouts.app')

@section('title', 'Customer Details')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Customer Name</span><span>{{ $customer->customer_name }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Customer Email</span><span>{{ $customer->customer_email }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Customer Phone</span><span>{{ $customer->customer_phone }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">City</span><span>{{ $customer->city }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Country</span><span>{{ $customer->country }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Address</span><span>{{ $customer->address }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

