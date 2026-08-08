@extends('layouts.app')

@section('title', __('expense-category.edit'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">{{ __('expense.expenses') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expense-categories.index') }}">{{ __('expense-category.categories') }}</a></li>
        <li class="breadcrumb-item active">{{ __('expense-category.edit') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-body">
                        <livewire:expense-categories.expense-category-form :expense-category="$expenseCategory"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
