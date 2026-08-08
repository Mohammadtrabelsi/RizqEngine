@extends('layouts.app')

@section('title', __('sales.sale_returns'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('sales.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('sales.sale_returns') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:sale-returns.sale-return-index/>
    </div>
@endsection
