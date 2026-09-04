@extends('layouts.app')

@section('title', __('settings.settings'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('settings.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('settings.mail_settings') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @include('utils.alerts')
                @include('setting._tabs')
                @if (session()->has('settings_smtp_message'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <div class="alert-body">
                            <span>{{ session('settings_smtp_message') }}</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ __('settings.mail_settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.smtp.update') }}" method="POST">
                            @csrf
                            @method('patch')
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_mailer">{{ __('settings.mail_mailer') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="mail_mailer" value="{{ env('MAIL_MAILER') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_host">{{ __('settings.mail_host') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="mail_host" value="{{ env('MAIL_HOST') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_port">{{ __('settings.mail_port') }} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="mail_port" value="{{ env('MAIL_PORT') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_username">{{ __('settings.mail_username') }}</label>
                                        <input type="text" class="form-control" name="mail_username" value="{{ env('MAIL_USERNAME') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_password">{{ __('settings.mail_password') }}</label>
                                        <input type="password" class="form-control" name="mail_password" value="{{ env('MAIL_PASSWORD') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_encryption">{{ __('settings.mail_encryption') }}</label>
                                        <input type="text" class="form-control" name="mail_encryption" value="{{ env('MAIL_ENCRYPTION') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="mail_from_address">{{ __('settings.mail_from_address') }}</label>
                                        <input type="email" class="form-control" name="mail_from_address" value="{{ env('MAIL_FROM_ADDRESS') }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="mail_from_name">{{ __('settings.mail_from_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="mail_from_name" value="{{ env('MAIL_FROM_NAME') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check"></i> {{ __('settings.save_changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
