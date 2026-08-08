@extends('layouts.app')

@section('title', __('expense.edit'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">{{ __('expense.expenses') }}</a></li>
        <li class="breadcrumb-item active">{{ __('expense.edit') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:expenses.expense-form :expense="$expense"/>
    </div>
@endsection
