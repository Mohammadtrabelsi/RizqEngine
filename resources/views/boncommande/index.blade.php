@extends('layouts.app')

@section('title', __('boncommande.bon_commandes'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('boncommande.bon_commandes') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        @include('utils.alerts')
        <livewire:bon-commandes.bon-commande-index/>
    </div>
@endsection
