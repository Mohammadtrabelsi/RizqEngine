@extends('errors.illustrated-layout')

@section('code', '404 😵')

@section('title', __('Page Not Found'))

@section('image')
    <div class="absolute pin bg-no-repeat md:bg-left lg:bg-center bg-cover bg-error-illustration"></div>
@endsection

@section('message', __('Sorry, the page you are looking for could not be found.'))
