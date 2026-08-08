@extends('layouts.app')

@section('title', __('customer.create'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">{{ __('customer.customers') }}</a></li>
        <li class="breadcrumb-item active">{{ __('customer.create') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:customers.customer-form/>
    </div>
@endsection

