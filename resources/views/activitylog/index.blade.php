@extends('layouts.app')

@section('title', __('activitylog.activity_logs'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('activitylog.activity_logs') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:activity-logs.activity-log-index/>
    </div>
@endsection
