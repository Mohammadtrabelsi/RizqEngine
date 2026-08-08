@extends('layouts.app')

@section('title', __('customer.edit'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">{{ __('customer.customers') }}</a></li>
        <li class="breadcrumb-item active">{{ __('customer.edit') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:customers.customer-form :customer="$customer"/>
    </div>
@endsection

