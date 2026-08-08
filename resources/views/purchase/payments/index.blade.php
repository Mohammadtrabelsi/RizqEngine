@extends('layouts.app')

@section('title', __('purchase.payments'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('purchase.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">{{ __('purchase.purchases') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchases.show', $purchase) }}">{{ $purchase->reference }}</a></li>
        <li class="breadcrumb-item active">{{ __('purchase.payments') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:purchases.purchase-payment-index :purchaseId="$purchase->id"/>
    </div>
@endsection
