@extends('layouts.app')

@section('title', __('stockexit.stock_exits'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('stockexit.stock_exits') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        @include('utils.alerts')
        <livewire:stock-exits.stock-exit-index/>
    </div>
@endsection
