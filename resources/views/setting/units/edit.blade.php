@extends('layouts.app')

@section('title', __('units.edit_unit'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('units.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('units.index') }}">{{ __('units.units') }}</a></li>
        <li class="breadcrumb-item active">{{ __('units.edit_unit') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:units.unit-form :unit="$unit"/>
    </div>
@endsection

