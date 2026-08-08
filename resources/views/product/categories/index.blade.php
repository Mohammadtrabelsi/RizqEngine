@extends('layouts.app')

@section('title', __('product.categories'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('product.products') }}</a></li>
        <li class="breadcrumb-item active">{{ __('product.categories') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:product-categories.category-index/>
    </div>
@endsection
