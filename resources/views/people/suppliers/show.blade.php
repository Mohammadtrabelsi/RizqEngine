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
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.supplier_name') }}</span><span>{{ $supplier->supplier_name }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.supplier_email') }}</span><span>{{ $supplier->supplier_email }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.supplier_phone') }}</span><span>{{ $supplier->supplier_phone }}</span></li>
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

