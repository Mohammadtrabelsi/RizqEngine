@extends('layouts.app')

@section('title', __('sales.payments'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('sales.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">{{ __('sales.sales') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.show', $sale) }}">{{ $sale->reference }}</a></li>
        <li class="breadcrumb-item active">{{ __('sales.payments') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:sales.sale-payment-index :saleId="$sale->id"/>
    </div>
@endsection
