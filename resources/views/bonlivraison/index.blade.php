@extends('layouts.app')

@section('title', __('bonlivraison.bon_livraisons'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('bonlivraison.bon_livraisons') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        @include('utils.alerts')
        <livewire:bon-livraisons.bon-livraison-index/>
    </div>
@endsection
