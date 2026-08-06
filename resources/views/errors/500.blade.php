@extends('errors.illustrated-layout')

@section('code', '500 🤕')

@section('title', __('Server Error'))

@section('image')
    <div class="absolute pin bg-no-repeat md:bg-left lg:bg-center bg-cover bg-error-illustration"></div>
@endsection

@section('message', __('Something went wrong. Call the dev!!!'))
