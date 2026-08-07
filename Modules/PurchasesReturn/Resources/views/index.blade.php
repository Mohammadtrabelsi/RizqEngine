@extends('layouts.app')

@section('title', 'Purchase Returns')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Purchase Returns</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('purchase-returns.create') }}" class="btn btn-primary">
                    Add Purchase Return <i class="bi bi-plus"></i>
                </a>
            </div>
        </div>

        <div class="row">
            @forelse($purchase_returns as $purchase_return)
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="fw-bold">{{ $purchase_return->reference }}</span>
                            @include('purchasesreturn::partials.status', ['data' => $purchase_return])
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3"><i class="bi bi-truck"></i> {{ $purchase_return->supplier_name }}</h6>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Total</span><span>{{ format_currency($purchase_return->total_amount) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Paid</span><span>{{ format_currency($purchase_return->paid_amount) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Due</span><span>{{ format_currency($purchase_return->due_amount) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Payment Status</span>@include('purchasesreturn::partials.payment-status', ['data' => $purchase_return])</li>
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            @include('purchasesreturn::partials.actions', ['data' => $purchase_return])
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">No purchase returns found.</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $purchase_returns->links() }}
        </div>
    </div>
@endsection
