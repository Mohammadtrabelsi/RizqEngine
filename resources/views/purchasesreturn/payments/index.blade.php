@extends('layouts.app')

@section('title', __('purchase-returns.payments'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}"> {{ __('general.home') }} </a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchase-returns.index') }}"> {{ __('purchase-returns.purchase_returns') }} </a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchases.show', $purchase_return) }}">{{ $purchase_return->reference }}</a></li>
        <li class="breadcrumb-item active"> {{ __('purchase-returns.payments') }} </li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:purchase-returns.purchase-return-payment-index :purchaseReturnId="$purchase_return->id"/>
    </div>
@endsection
