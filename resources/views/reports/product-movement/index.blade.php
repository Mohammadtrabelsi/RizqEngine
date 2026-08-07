@extends('layouts.app')

@section('title', 'Fast / Slow Moving Products Report')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Fast / Slow Moving Products</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:reports.product-movement-report/>
    </div>
@endsection
