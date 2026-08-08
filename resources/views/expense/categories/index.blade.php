@extends('layouts.app')

@section('title', __('expense-category.categories'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">{{ __('expense.expenses') }}</a></li>
        <li class="breadcrumb-item active">{{ __('expense-category.categories') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:expense-categories.expense-category-index/>
    </div>
@endsection
