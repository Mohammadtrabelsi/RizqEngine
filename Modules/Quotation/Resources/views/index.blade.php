@extends('layouts.app')

@section('title', 'Quotations')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Quotations</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('quotations.create') }}" class="btn btn-primary">
                    Add Quotation <i class="bi bi-plus"></i>
                </a>
            </div>
        </div>

        <div class="row">
            @forelse($quotations as $quotation)
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="fw-bold">{{ $quotation->reference }}</span>
                            @include('quotation::partials.status', ['data' => $quotation])
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3"><i class="bi bi-person"></i> {{ $quotation->customer_name }}</h6>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Date</span><span>{{ $quotation->date }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>Total</span><span>{{ format_currency($quotation->total_amount) }}</span></li>
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            @include('quotation::partials.actions', ['data' => $quotation])
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">No quotations found.</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $quotations->links() }}
        </div>
    </div>
@endsection
