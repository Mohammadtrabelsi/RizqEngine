@extends('errors.illustrated-layout')

@section('code', '403 🤐')

@section('title', __('Unauthorized'))

@section('image')
    <div class="absolute pin bg-no-repeat md:bg-left lg:bg-center bg-cover bg-error-illustration"></div>
@endsection

@section('message', __('Sorry, you don\'t have the permission to visit this page.'))
