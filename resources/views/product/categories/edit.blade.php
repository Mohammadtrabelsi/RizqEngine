@extends('layouts.app')

@section('title', __('product.edit_category'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('product.products') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('product-categories.index') }}">{{ __('product.categories') }}</a></li>
        <li class="breadcrumb-item active">{{ __('product.edit_category') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-body">
                        <livewire:product-categories.category-form :category="$category"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
