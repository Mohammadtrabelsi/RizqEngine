@extends('layouts.app')

@section('title', __('sales.payments'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('sales.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sale-returns.index') }}">{{ __('sales.sale_returns') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.show', $sale_return) }}">{{ $sale_return->reference }}</a></li>
        <li class="breadcrumb-item active">{{ __('sales.payments') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:sale-returns.sale-return-payment-index :saleReturnId="$sale_return->id"/>
    </div>
@endsection
