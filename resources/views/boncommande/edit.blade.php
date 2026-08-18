@extends('layouts.app')

@section('title', __('boncommande.edit_bon_commande'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('bon-commandes.index') }}">{{ __('boncommande.bon_commandes') }}</a></li>
        <li class="breadcrumb-item active">{{ __('boncommande.edit') }}</li>
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
                        <form id="bon-commande-form" action="{{ route('bon-commandes.update', $bonCommande) }}" method="POST">
                            @csrf
                            @method('patch')
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="reference">{{ __('boncommande.reference') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference" required value="{{ $bonCommande->reference }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="customer_id">{{ __('boncommande.customer') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" name="customer_id" id="customer_id" required>
                                            @foreach(\App\Models\Customer::all() as $customer)
                                                <option {{ $bonCommande->customer_id == $customer->id ? 'selected' : '' }} value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="date">{{ __('boncommande.date') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" required value="{{ $bonCommande->getAttributes()['date'] }}">
                                    </div>
                                </div>
                            </div>

                            <livewire:product-cart :cartInstance="'bon_commande'" :data="$bonCommande"/>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="status">{{ __('boncommande.status_label') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" name="status" id="status" required>
                                            <option {{ $bonCommande->status == 'draft' ? 'selected' : '' }} value="draft">{{ __('boncommande.status_draft') }}</option>
                                            <option {{ $bonCommande->status == 'confirmed' ? 'selected' : '' }} value="confirmed">{{ __('boncommande.status_confirmed') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="note">{{ __('boncommande.note') }}</label>
                                <textarea name="note" id="note" rows="5" class="form-control">{{ $bonCommande->note }}</textarea>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('boncommande.update') }} <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')

@endpush
