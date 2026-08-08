@extends('layouts.app')

@section('title', __('units.units'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('units.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('units.units') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:units.unit-index/>
    </div>
@endsection
