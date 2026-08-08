@extends('layouts.app')

@section('title', __('expense.create'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">{{ __('expense.expenses') }}</a></li>
        <li class="breadcrumb-item active">{{ __('expense.create') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:expenses.expense-form/>
    </div>
@endsection
