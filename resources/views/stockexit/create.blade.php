@extends('layouts.app')

@section('title', __('stockexit.create_exit'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('stock-exits.index') }}">{{ __('stockexit.stock_exits') }}</a></li>
        <li class="breadcrumb-item active">{{ __('stockexit.create_exit') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-12">
                <livewire:search-product/>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @include('utils.alerts')
                        <form action="{{ route('stock-exits.store') }}" method="POST">
                            @csrf
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>{{ __('stockexit.reference') }}</label>
                                        <input type="text" class="form-control" readonly value="{{ __('stockexit.auto_generated') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="date">{{ __('stockexit.date') }} <span class="text-danger">*</span></label>
                                        <input type="date" id="date" class="form-control" name="date" required value="{{ old('date', now()->format('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="reason">{{ __('stockexit.reason') }}</label>
                                        <select name="reason" id="reason" class="form-control">
                                            <option value="Prêt">{{ __('stockexit.reason_loan') }}</option>
                                            <option value="Chantier">{{ __('stockexit.reason_jobsite') }}</option>
                                            <option value="Sous-traitance">{{ __('stockexit.reason_subcontracting') }}</option>
                                            <option value="Transfert">{{ __('stockexit.reason_transfer') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="destination">{{ __('stockexit.destination') }}</label>
                                        <input type="text" id="destination" class="form-control" name="destination" value="{{ old('destination') }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="responsible">{{ __('stockexit.responsible') }}</label>
                                        <input type="text" id="responsible" class="form-control" name="responsible" value="{{ old('responsible') }}">
                                    </div>
                                </div>
                            </div>

                            <livewire:stock-exit.product-table/>

                            <div class="form-group">
                                <label for="note">{{ __('stockexit.note') }}</label>
                                <textarea name="note" id="note" rows="4" class="form-control">{{ old('note') }}</textarea>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('stockexit.create_exit') }} <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
