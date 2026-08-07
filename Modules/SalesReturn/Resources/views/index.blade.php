@extends('layouts.app')

@section('title', 'Sale Returns')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Sale Returns</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('sale-returns.create') }}" class="btn btn-primary">
                    Add Sale Return <i class="bi bi-plus"></i>
                </a>
            </div>
        </div>

        <div class="row">
            @forelse($sale_returns as $sale_return)
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="fw-bold">{{ $sale_return->reference }}</span>
                            @include('salesreturn::partials.status', ['data' => $sale_return])
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3"><i class="bi bi-person"></i> {{ $sale_return->customer_name }}</h6>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Total</span><span>{{ format_currency($sale_return->total_amount) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Paid</span><span>{{ format_currency($sale_return->paid_amount) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Due</span><span>{{ format_currency($sale_return->due_amount) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Payment Status</span>@include('salesreturn::partials.payment-status', ['data' => $sale_return])</li>
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            @include('salesreturn::partials.actions', ['data' => $sale_return])
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">No sale returns found.</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $sale_returns->links() }}
        </div>
    </div>
@endsection
