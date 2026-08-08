@extends('layouts.app')

@section('title',   __('users.users'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('users.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('users.users') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:users.user-index/>
    </div>
@endsection
