@extends('layouts.app')

@section('title', __('quotations.add_quotation'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('common.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('quotations.quotations') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:quotations.quotation-index/>
    </div>
@endsection
