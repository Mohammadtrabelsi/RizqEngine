@extends('layouts.app')

@section('title', __('reports.expense_by_driver'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('common.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('reports.expense_by_driver') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:reports.expense-by-driver-report/>
    </div>
@endsection
