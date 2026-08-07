@extends('layouts.app')

@section('title', __('purchase.purchases'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('purchase.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('purchase.purchases') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                    {{ __('purchase.add_purchase') }} <i class="bi bi-plus"></i>
                </a>
            </div>
        </div>

        <div class="row">
            @forelse($purchases as $purchase)
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="fw-bold">{{ $purchase->reference }}</span>
                            @include('purchase::partials.status', ['data' => $purchase])
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3"><i class="bi bi-truck"></i> {{ $purchase->supplier_name }}</h6>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase.total') }}</span><span>{{ format_currency($purchase->total_amount) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase.paid') }}</span><span>{{ format_currency($purchase->paid_amount) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase.due') }}</span><span>{{ format_currency($purchase->due_amount) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase.payment_status') }}</span>@include('purchase::partials.payment-status', ['data' => $purchase])</li>
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            @include('purchase::partials.actions', ['data' => $purchase])
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">{{ __('purchase.no_purchases_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $purchases->links() }}
        </div>
    </div>
@endsection
