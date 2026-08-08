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
        <livewire:customers.customer-index/>
    </div>
@endsection
