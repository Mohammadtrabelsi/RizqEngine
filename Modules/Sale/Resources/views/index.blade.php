@extends('layouts.app')

@section('title', 'Sales')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Sales</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('sales.create') }}" class="btn btn-primary">
                    Add Sale <i class="bi bi-plus"></i>
                </a>
            </div>
        </div>

        <div class="row">
            @forelse($sales as $sale)
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="fw-bold">{{ $sale->reference }}</span>
                            @include('sale::partials.status', ['data' => $sale])
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3"><i class="bi bi-person"></i> {{ $sale->customer_name }}</h6>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Total</span><span>{{ format_currency($sale->total_amount) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Paid</span><span>{{ format_currency($sale->paid_amount) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Due</span><span>{{ format_currency($sale->due_amount) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Payment Status</span>@include('sale::partials.payment-status', ['data' => $sale])</li>
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            @include('sale::partials.actions', ['data' => $sale])
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">No sales found.</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $sales->links() }}
        </div>
    </div>
@endsection
