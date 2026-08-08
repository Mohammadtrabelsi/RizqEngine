@extends('layouts.app')

@section('title', __('settings.settings'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('settings.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('settings.settings') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @include('utils.alerts')
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ __('settings.general_settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('patch')
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="company_name">{{ __('settings.company_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="company_name" value="{{ $settings->company_name }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="company_email">{{ __('settings.company_email') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="company_email" value="{{ $settings->company_email }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="company_phone">{{ __('settings.company_phone') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="company_phone" value="{{ $settings->company_phone }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="client_name">Client Name</label>
                                        <input type="text" class="form-control" name="client_name" value="{{ $settings->client_name }}" placeholder="Displayed in the navbars">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="client_logo">Client Logo</label>
                                        <input type="file" class="form-control-file" name="client_logo" id="client_logo" accept="image/*">
                                        <small class="form-text text-muted">Displayed in the navbars. Max 2MB (jpeg, png, gif, svg, webp).</small>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="d-block">Current Logo</label>
                                        @if($settings->client_logo)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($settings->client_logo) }}" alt="Client Logo" style="max-height: 48px; max-width: 160px;">
                                        @else
                                            <span class="text-muted">No logo uploaded</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="default_currency_id">Default Currency <span class="text-danger">*</span></label>
                                        <select name="default_currency_id" id="default_currency_id" class="form-control" required>
                                            @foreach(\Modules\Currency\Entities\Currency::all() as $currency)
                                                <option {{ $settings->default_currency_id == $currency->id ? 'selected' : '' }} value="{{ $currency->id }}">{{ $currency->currency_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="default_currency_position">{{ __('settings.default_currency_position') }} <span class="text-danger">*</span></label>
                                        <select name="default_currency_position" id="default_currency_position" class="form-control" required>
                                            <option {{ $settings->default_currency_position == 'prefix' ? 'selected' : '' }} value="prefix">Prefix</option>
                                            <option {{ $settings->default_currency_position == 'suffix' ? 'selected' : '' }} value="suffix">Suffix</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="notification_email">{{ __('settings.notification_email') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="notification_email" value="{{ $settings->notification_email }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="company_address">{{ __('settings.company_address') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="company_address" value="{{ $settings->company_address }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="default_product_image">{{ __('settings.default_product_image') }}</label>
                                        <input type="file" class="form-control-file" name="default_product_image" id="default_product_image" accept="image/*">
                                        <small class="form-text text-muted">{{ __('settings.default_image_hint') }}</small>
                                        <div class="mt-2">
                                            <img src="{{ default_product_image() }}" alt="{{ __('settings.default_product_image') }}" class="img-thumbnail" width="80">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="default_category_image">{{ __('settings.default_category_image') }}</label>
                                        <input type="file" class="form-control-file" name="default_category_image" id="default_category_image" accept="image/*">
                                        <small class="form-text text-muted">{{ __('settings.default_image_hint') }}</small>
                                        <div class="mt-2">
                                            <img src="{{ default_category_image() }}" alt="{{ __('settings.default_category_image') }}" class="img-thumbnail" width="80">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check"></i> Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                @if (session()->has('settings_smtp_message'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <div class="alert-body">
                            <span>{{ session('settings_smtp_message') }}</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
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
                                        <label for="mail_mailer">{{ __('settings.mail_mailer') }}</label>
                                        <input type="text" class="form-control" name="mail_mailer" value="{{ env('MAIL_MAILER') }}">
                                    </div>
                                </div>
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
                            </div>

                            <div class="form-row">
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="mail_encryption">{{ __('settings.mail_encryption') }}</label>
                                        <input type="text" class="form-control" name="mail_encryption" value="{{ env('MAIL_ENCRYPTION') }}">
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label for="mail_from_address">{{ __('settings.mail_from_address') }}</label>
                                        <input type="email" class="form-control" name="mail_from_address" value="{{ env('MAIL_FROM_ADDRESS') }}">
                                    </div>
                                </div>
                                <div class="col-lg-5">
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

