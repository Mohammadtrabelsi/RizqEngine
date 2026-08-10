@extends('layouts.app')

@section('title', __('supplier.supplier_details'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">{{ __('supplier.suppliers') }}</a></li>
        <li class="breadcrumb-item active">{{ __('supplier.supplier_details') }}    </li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @if ($supplier->getFirstMediaUrl('images'))
                            <div class="text-center mb-4">
                                <img src="{{ $supplier->getFirstMediaUrl('images') }}" class="img-fluid img-thumbnail rounded-circle" alt="Supplier Image" style="max-width: 180px;">
                            </div>
                        @endif
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.supplier_name') }}</span><span>{{ $supplier->supplier_name }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.supplier_email') }}</span><span>{{ $supplier->supplier_email }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.supplier_phone') }}</span><span>{{ $supplier->supplier_phone }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.tax_identification_number') }}</span><span>{{ $supplier->tax_identification_number ?: '—' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.city') }}</span><span>{{ $supplier->city }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.country') }}</span><span>{{ $supplier->country }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.address') }}</span><span>{{ $supplier->address }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

