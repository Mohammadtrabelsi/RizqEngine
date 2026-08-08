@extends('layouts.app')

@section('title', __('supplier.suppliers'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('supplier.suppliers') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                    {{ __('supplier.create_supplier') }} <i class="bi bi-plus"></i>
                </a>
            </div>
        </div>

        <div class="row">
            @forelse($suppliers as $supplier)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $supplier->supplier_name }}</h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item px-0"><i class="bi bi-envelope"></i> {{ $supplier->supplier_email }}</li>
                                <li class="list-group-item px-0"><i class="bi bi-telephone"></i> {{ $supplier->supplier_phone }}</li>
                            </ul>
                            <div class="btn-group">
                                @include('people.suppliers.partials.actions', ['data' => $supplier])
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">{{ __('supplier.no_suppliers_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $suppliers->links() }}
        </div>
    </div>
@endsection
