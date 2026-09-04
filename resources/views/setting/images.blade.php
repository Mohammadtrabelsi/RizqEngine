@extends('layouts.app')

@section('title', __('settings.settings'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('settings.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('settings.default_product_image') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @include('utils.alerts')
                @include('setting._tabs')
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ __('settings.default_product_image') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.images.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('patch')
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

                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="default_supplier_image">{{ __('settings.default_supplier_image') }}</label>
                                        <input type="file" class="form-control-file" name="default_supplier_image" id="default_supplier_image" accept="image/*">
                                        <small class="form-text text-muted">{{ __('settings.default_image_hint') }}</small>
                                        <div class="mt-2">
                                            <img src="{{ default_supplier_image() }}" alt="{{ __('settings.default_supplier_image') }}" class="img-thumbnail" width="80">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="default_customer_image">{{ __('settings.default_customer_image') }}</label>
                                        <input type="file" class="form-control-file" name="default_customer_image" id="default_customer_image" accept="image/*">
                                        <small class="form-text text-muted">{{ __('settings.default_image_hint') }}</small>
                                        <div class="mt-2">
                                            <img src="{{ default_customer_image() }}" alt="{{ __('settings.default_customer_image') }}" class="img-thumbnail" width="80">
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
        </div>
    </div>
@endsection
