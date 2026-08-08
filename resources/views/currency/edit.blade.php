@extends('layouts.app')

@section('title', __('currency.edit_currency'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('currencies.index') }}">{{ __('currency.currencies') }}</a></li>
        <li class="breadcrumb-item active">{{ __('currency.edit_currency') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:currencies.currency-form :currency="$currency"/>
    </div>
@endsection
