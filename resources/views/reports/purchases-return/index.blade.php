@extends('layouts.app')

@section('title', __('reports.purchases_return'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('common.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('reports.purchases_return') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:reports.purchases-return-report :suppliers="\App\Models\Supplier::all()"/>
    </div>
@endsection
