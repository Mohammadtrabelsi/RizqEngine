@extends('layouts.app')

@section('title', __('purchase-returns.purchase_returns'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('purchase-returns.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('purchase-returns.purchase_returns') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:purchase-returns.purchase-return-index/>
    </div>
@endsection
