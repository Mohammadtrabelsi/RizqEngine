@extends('layouts.app')

@section('title', __('supplier.edit_supplier'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">{{ __('supplier.suppliers') }}</a></li>
        <li class="breadcrumb-item active">{{ __('supplier.edit_supplier') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:suppliers.supplier-form :supplier="$supplier"/>
    </div>
@endsection

