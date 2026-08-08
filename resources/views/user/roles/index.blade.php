@extends('layouts.app')

@section('title', __('roles.roles_and_permissions'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('roles.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('roles.roles_and_permissions') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:roles.role-index/>
    </div>
@endsection
