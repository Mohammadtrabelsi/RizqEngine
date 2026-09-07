@extends('layouts.app')

@section('title', __('stockexit.declare_return'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('stock-exits.index') }}">{{ __('stockexit.stock_exits') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('stock-exits.show', $stockExit->id) }}">{{ $stockExit->reference }}</a></li>
        <li class="breadcrumb-item active">{{ __('stockexit.declare_return') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        @include('utils.alerts')
        <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
            <div class="flex-auto p-5">
                <h4 class="mb-1">{{ __('stockexit.bon_dentree') }}</h4>
                <p class="text-muted">{{ __('stockexit.origin_reference') }}: <strong>{{ $stockExit->reference }}</strong></p>
                @if($stockExit->isConsignment() && $stockExit->customer)
                    <p class="text-muted">{{ __('stockexit.consignee') }}: <strong>{{ $stockExit->customer->customer_name }}</strong></p>
                @endif

                <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-indigo-50 text-indigo-700 border-indigo-200">
                    <i class="bi bi-info-circle"></i>
                    {{ $stockExit->isConsignment() ? __('stockexit.consignment_return_hint') : __('stockexit.return_control_hint') }}
                </div>

                <form action="{{ route('stock-entries.store', $stockExit->id) }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="col-lg-4">
                            <div class="mb-4">
                                <label for="date">{{ __('stockexit.reception_date') }} <span class="text-danger">*</span></label>
                                <input type="date" id="date" name="date" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" required value="{{ old('date', now()->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>

                    <div class="block w-full overflow-x-auto">
                        <table class="w-full mb-4 text-slate-900 border-collapse table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('product.code') }}</th>
                                    <th>{{ __('product.name') }}</th>
                                    <th class="text-end">{{ __('stockexit.quantity_out') }}</th>
                                    <th class="text-end">{{ __('stockexit.quantity_outstanding') }}</th>
                                    <th class="img-w-180">{{ __('stockexit.quantity_received') }}</th>
                                    <th class="img-w-180">{{ __('stockexit.quantity_lost') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockExit->details as $detail)
                                    @continue($detail->outstanding_quantity <= 0)
                                    <tr>
                                        <td>{{ $detail->product->product_code }}</td>
                                        <td>{{ translatable_string($detail->product->product_name) }}</td>
                                        <td class="text-end">{{ $detail->quantity }}</td>
                                        <td class="text-end">{{ $detail->outstanding_quantity }}</td>
                                        <td>
                                            <input type="hidden" name="detail_ids[]" value="{{ $detail->id }}">
                                            <input type="number" name="returned[]" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45"
                                                   min="0" max="{{ $detail->outstanding_quantity }}"
                                                   value="{{ $detail->outstanding_quantity }}" required>
                                        </td>
                                        <td>
                                            <input type="number" name="lost[]" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45"
                                                   min="0" max="{{ $detail->outstanding_quantity }}"
                                                   value="0">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mb-4">
                        <label for="note">{{ __('stockexit.note') }}</label>
                        <textarea name="note" id="note" rows="3" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">{{ old('note') }}</textarea>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-emerald-500 !text-white border-emerald-500 hover:bg-emerald-600 hover:border-emerald-600">
                            {{ __('stockexit.confirm_return') }} <i class="bi bi-check"></i>
                        </button>
                        <a href="{{ route('stock-exits.show', $stockExit->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-white !text-slate-700 border-slate-300 hover:bg-slate-50 hover:!text-slate-900 hover:border-slate-400">{{ __('app.back') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
