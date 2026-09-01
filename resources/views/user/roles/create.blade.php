@extends('layouts.app')

@section('title', __('roles.create_role'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}"> {{ __('common.home') }} </a></li>
        <li class="breadcrumb-item"><a href="{{ route('roles.index') }}"> {{ __('roles.roles') }} </a></li>
        <li class="breadcrumb-item active"> {{ __('roles.create_role') }} </li>
    </ol>
@endsection

@push('page_css')
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <livewire:roles.role-form/>
            </div>
        </div>
    </div>
@endsection
