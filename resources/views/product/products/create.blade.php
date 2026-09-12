@extends('layouts.app')

@section('title', __('product.add_product'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('product.products') }}</a></li>
        <li class="breadcrumb-item active">{{ __('product.add_product') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form id="product-form" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-12">
                    @include('utils.alerts')
                    <div class="mb-4">
                        <button class="btn btn-primary">{{ __('product.create_product') }} <i class="bi bi-check"></i></button>
                    </div>
                </div>
                <div class="col-12 col-lg-8 mb-4">
                    <div class="card">
                        <div class="flex-auto p-2">
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="product_name">{{ __('product.product_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="product_name" required value="{{ old('product_name') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="product_code">{{ __('product.product_code') }} <span class="text-danger">*</span></label>
                                        <input type="number" min="0" step="1" class="form-control" name="product_code" required value="{{ old('product_code') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6">
                                    <label for="category_id">{{ __('product.category') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select class="form-control" name="category_id" id="category_id" required>
                                            <option value="" selected disabled>Select Category</option>
                                            @foreach(\App\Models\Category::all() as $category)
                                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append d-flex">
                                            <a href="{{ route('product-categories.create') }}" target="_blank" rel="noopener" class="btn btn-outline">
                                                Add
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="supplier_id">{{ __('product.supplier') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" name="supplier_id" id="supplier_id" required>
                                            <option value="" selected disabled>{{ __('product.select_supplier') }}</option>
                                            @foreach(\App\Models\Supplier::all() as $supplier)
                                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->supplier_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="barcode_symbology">{{ __('product.barcode_symbology') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" name="product_barcode_symbology" id="barcode_symbology" required>
                                            <option value="" selected disabled>{{ __('product.select_symbology') }}</option>
                                            <option value="C128">{{ __('product.code_128') }}</option>
                                            <option value="C39">{{ __('product.code_39') }}</option>
                                            <option value="UPCA">{{ __('product.upc_a') }}</option>
                                            <option value="UPCE">{{ __('product.upc_e') }}</option>
                                            <option selected value="EAN13">{{ __('product.ean_13') }}</option>
                                            <option value="EAN8">{{ __('product.ean_8') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            @php($pricingMode = old('pricing_mode', 'price'))
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="mb-4">
                                        <label class="d-block">{{ __('product.pricing_mode') }}</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="pricing_mode" id="pricing_mode_price" value="price" {{ $pricingMode === 'margin' ? '' : 'checked' }}>
                                            <label class="form-check-label" for="pricing_mode_price">{{ __('product.pricing_mode_price') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="pricing_mode" id="pricing_mode_margin" value="margin" {{ $pricingMode === 'margin' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pricing_mode_margin">{{ __('product.pricing_mode_margin') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="product_cost">{{ __('product.cost') }} <span class="text-danger">*</span></label>
                                        <input id="product_cost" type="text" class="form-control" name="product_cost" data-money-mask required value="{{ old('product_cost') }}">
                                    </div>
                                </div>
                                <div class="col-md-6" id="product_margin_wrapper" style="display:none;">
                                    <div class="mb-4">
                                        <label for="product_margin">{{ __('product.margin') }}</label>
                                        <input id="product_margin" type="number" step="0.01" min="0" class="form-control" name="product_margin" value="{{ old('product_margin') }}">
                                        <small class="block mt-1 text-xs text-slate-500 text-muted">{{ __('product.margin_help') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="product_price">{{ __('product.price') }} <span class="text-danger">*</span></label>
                                        <input id="product_price" type="text" class="form-control" name="product_price" data-money-mask required value="{{ old('product_price') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="product_quantity">{{ __('product.quantity') }} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="product_quantity" required value="{{ old('product_quantity') }}" min="1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="product_stock_alert">{{ __('product.stock_alert') }} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="product_stock_alert" required value="{{ old('product_stock_alert', 10) }}" min="10" max="100">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="product_stock_alert_max">{{ __('product.stock_alert_max') }}</label>
                                        <input type="number" class="form-control" name="product_stock_alert_max" value="{{ old('product_stock_alert_max') }}" min="1">
                                        <small class="block mt-1 text-xs text-slate-500 text-muted">{{ __('product.stock_alert_max_help') }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="product_tax_type">{{ __('product.tax_type') }}</label>
                                        <select class="form-control" name="product_tax_type" id="product_tax_type">
                                            <option value="" selected >{{ __('product.select_tax_type') }}</option>
                                            <option value="1">{{ __('product.exclusive') }}</option>
                                            <option value="2">{{ __('product.inclusive') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="product_unit">{{ __('product.unit') }} <i class="bi bi-question-circle-fill text-info" data-toggle="tooltip" data-placement="top" title="This short text will be placed after Product Quantity."></i> <span class="text-danger">*</span></label>
                                        <select class="form-control" name="product_unit" id="product_unit">
                                            <option value="" selected >{{ __('product.select_unit') }}</option>
                                            @foreach(\App\Models\Unit::all() as $unit)
                                                <option value="{{ $unit->short_name }}">{{ $unit->name . ' | ' . $unit->short_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row" id="product_db_taxes_wrapper" style="display:none;">
                                <div class="col-md-12">
                                    <div class="mb-4">
                                        <label>{{ __('taxes.select_taxes') }} <span id="product-tax-total" class="text-muted"></span></label>
                                        @forelse($productTaxes as $tax)
                                            <div class="form-check">
                                                <input class="form-check-input product-tax-checkbox" type="checkbox" name="product_taxes[]" value="{{ $tax->id }}" data-type="{{ $tax->type }}" data-rate="{{ $tax->rate }}" id="product-tax-{{ $tax->id }}">
                                                <label class="form-check-label" for="product-tax-{{ $tax->id }}">
                                                    {{ $tax->name }} ({{ $tax->type === 'fixed' ? format_currency($tax->rate) : rtrim(rtrim(number_format($tax->rate, 2), '0'), '.').'%' }})
                                                </label>
                                            </div>
                                        @empty
                                            <p class="text-muted mb-0">
                                                {{ __('taxes.no_taxes_defined') }}
                                                <a href="{{ route('taxes.create') }}" target="_blank">{{ __('taxes.add_tax') }}</a>
                                            </p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="expiry_date">{{ __('product.expiry_date') }}</label>
                                        <input type="date" class="form-control" name="expiry_date" id="expiry_date" value="{{ old('expiry_date') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="product_note">{{ __('product.note') }}</label>
                                <textarea name="product_note" id="product_note" rows="4 " class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4 mb-4">
                    <div class="card">
                        <div class="flex-auto p-2">
                            <div class="mb-4">
                                <label for="image">{{ __('product.images') }} <i class="bi bi-question-circle-fill text-info" data-toggle="tooltip" data-placement="top" title="Max Files: 3, Max File Size: 1MB, Image Size: 400x400"></i></label>
                                <div class="dropzone d-flex flex-wrap align-items-center justify-content-center" id="document-dropzone">
                                    <div class="dz-message text-center" data-dz-message>
                                        <i class="bi bi-cloud-arrow-up d-block"></i>
                                        <span class="btn btn-primary mt-2">{{ __('product.choose_image') }}</span>
                                        <p class="text-muted mt-2 mb-0">{{ __('product.drop_image_hint') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection


@push('page_scripts')
    @include('includes.product-dropzone-js')
    @include('includes.money-mask-js')
    @include('includes.product-tax-picker-js')
    @include('includes.product-pricing-mode-js')
@endpush

