@extends('errors.illustrated-layout')

@section('code', '419 👾')

@section('title', __('Page Expired'))

@section('image')
    <div class="absolute pin bg-no-repeat md:bg-left lg:bg-center bg-cover bg-error-illustration"></div>
@endsection

@section('message', __('Maybe, the CSRF token is missing.'))
