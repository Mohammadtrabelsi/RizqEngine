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
        <div class="col-12 col-md-6 mb-3">
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                {{ __('supplier.create_supplier') }} <i class="bi bi-plus"></i>
            </a>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $suppliers->links() }}</div>
    <div class="row">
        @forelse($suppliers as $supplier)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="supplier-{{ $supplier->id }}">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $supplier->supplier_name }}</h5>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item px-0"><i class="bi bi-envelope"></i> {{ $supplier->supplier_email }}</li>
                            <li class="list-group-item px-0"><i class="bi bi-telephone"></i> {{ $supplier->supplier_phone }}</li>
                            @if ($supplier->tax_identification_number)
                                <li class="list-group-item px-0"><i class="bi bi-receipt"></i> {{ $supplier->tax_identification_number }}</li>
                            @endif
                        </ul>
                        <div class="btn-group">
                            @can('edit_suppliers')
                                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-info btn-sm"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('show_suppliers')
                                <a href="{{ route('suppliers.show', $supplier->id) }}" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i></a>
                            @endcan
                            @can('delete_suppliers')
                                <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $supplier->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            @endcan
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
