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
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
                    <div class="flex-auto p-5">
                        @include('utils.alerts')
                        <form action="{{ route('stock-exits.store') }}" method="POST">
                            @csrf
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label>{{ __('stockexit.reference') }}</label>
                                        <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" readonly value="{{ __('stockexit.auto_generated') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="date">{{ __('stockexit.date') }} <span class="text-danger">*</span></label>
                                        <input type="date" id="date" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="date" required value="{{ old('date', now()->format('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="reason">{{ __('stockexit.reason') }}</label>
                                        <select name="reason" id="reason" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
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
                                    <div class="mb-4">
                                        <label for="kind">{{ __('stockexit.kind') }} <span class="text-danger">*</span></label>
                                        <select name="kind" id="kind" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" onchange="document.getElementById('consignee-group').style.display = this.value === 'consignment' ? 'block' : 'none';">
                                            <option value="standard" @selected(old('kind', 'standard') === 'standard')>{{ __('stockexit.kind_standard') }}</option>
                                            <option value="consignment" @selected(old('kind') === 'consignment')>{{ __('stockexit.kind_consignment') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6" id="consignee-group" style="display: {{ old('kind') === 'consignment' ? 'block' : 'none' }};">
                                    <div class="mb-4">
                                        <label for="customer_id">{{ __('stockexit.consignee') }} <span class="text-danger">*</span></label>
                                        <select name="customer_id" id="customer_id" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                                            <option value="">{{ __('stockexit.select_consignee') }}</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->customer_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="destination">{{ __('stockexit.destination') }}</label>
                                        <input type="text" id="destination" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="destination" value="{{ old('destination') }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="responsible">{{ __('stockexit.responsible') }}</label>
                                        <input type="text" id="responsible" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="responsible" value="{{ old('responsible') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="driver_id">{{ __('stockexit.driver') }}</label>
                                        <select name="driver_id" id="driver_id" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                                            <option value="">{{ __('stockexit.select_driver') }}</option>
                                            @foreach($drivers as $driver)
                                                <option value="{{ $driver->id }}" @selected(old('driver_id') == $driver->id)>{{ $driver->name }}{{ $driver->phone ? ' — '.$driver->phone : '' }}</option>
                                            @endforeach
                                        </select>
                                        @can('create_drivers')
                                            <small class="block mt-1 text-xs text-slate-500"><a href="{{ route('drivers.create') }}" target="_blank">{{ __('drivers.add_driver') }}</a></small>
                                        @endcan
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="vehicle_id">{{ __('stockexit.vehicle') }}</label>
                                        <select name="vehicle_id" id="vehicle_id" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                                            <option value="">{{ __('stockexit.select_vehicle') }}</option>
                                            @foreach($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') == $vehicle->id)>{{ $vehicle->label }}</option>
                                            @endforeach
                                        </select>
                                        @can('create_vehicles')
                                            <small class="block mt-1 text-xs text-slate-500"><a href="{{ route('vehicles.create') }}" target="_blank">{{ __('vehicles.add_vehicle') }}</a></small>
                                        @endcan
                                    </div>
                                </div>
                            </div>

                            <livewire:stock-exit.product-table/>

                            <div class="mb-4">
                                <label for="note">{{ __('stockexit.note') }}</label>
                                <textarea name="note" id="note" rows="4" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">{{ old('note') }}</textarea>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
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
