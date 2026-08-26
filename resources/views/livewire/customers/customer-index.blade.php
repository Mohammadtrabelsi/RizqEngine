@extends('layouts.app')

@section('title', __('customer.customers'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('customer.customers') }}</li>
    </ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            @can('create_customers')
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    {{ __('customer.add_customer') }} <i class="bi bi-plus"></i>
                </a>
            @endcan
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="row align-items-end">
        <div class="col-12 col-md-3 mb-3">
            <label class="form-label small text-muted mb-1">{{ __('customer.city') }}</label>
            <select wire:model.live="city" class="form-select">
                <option value="">{{ __('app.all') }}</option>
                @foreach($cities as $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-3 mb-3">
            <label class="form-label small text-muted mb-1">{{ __('customer.country') }}</label>
            <select wire:model.live="country" class="form-select">
                <option value="">{{ __('app.all') }}</option>
                @foreach($countries as $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-3 mb-3">
            <label class="form-label small text-muted mb-1">{{ __('customer.tax_identification_number') }}</label>
            <select wire:model.live="hasTaxId" class="form-select">
                <option value="">{{ __('app.all') }}</option>
                <option value="yes">{{ __('app.yes') }}</option>
                <option value="no">{{ __('app.no') }}</option>
            </select>
        </div>
        <div class="col-12 col-md-3 mb-3">
            <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                <i class="bi bi-x-circle"></i> {{ __('app.reset') }}
            </button>
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $customers->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($customers as $customer)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="customer-{{ $customer->id }}">
                <div class="card h-100">
                    <div class="position-relative overflow-hidden rounded-top" style="height: 160px; background: #f8f9fa;">
                        @php($customerImage = $customer->getFirstMediaUrl('images'))
                        @if ($customerImage)
                            <img src="{{ $customerImage }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $customer->customer_name }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center w-100 h-100 text-muted">
                                <i class="bi bi-person-circle" style="font-size: 3.5rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $customer->customer_name }}</h5>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item px-0"><i class="bi bi-envelope"></i> {{ $customer->customer_email }}</li>
                            <li class="list-group-item px-0"><i class="bi bi-telephone"></i> {{ $customer->customer_phone }}</li>
                            @if ($customer->tax_identification_number)
                                <li class="list-group-item px-0"><i class="bi bi-receipt"></i> {{ $customer->tax_identification_number }}</li>
                            @endif
                        </ul>
                        <div class="btn-group">
                            @can('edit_customers')
                                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-outline-info btn-sm"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('show_customers')
                                <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                            @endcan
                            @can('delete_customers')
                                <button type="button" class="btn btn-outline-danger btn-sm" wire:click="delete({{ $customer->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-muted">{{ __('customer.no_customers_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $customers->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
