{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
<div class="container-fluid">
    {{-- Page-level actions --}}
    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
        <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> {{ __('customer.back_to_list') }}
        </a>
        @can('create_sales')
            <a href="{{ route('sales.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i> {{ __('customer.new_sale') }}
            </a>
        @endcan
        @can('update_customers')
            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-pencil me-1"></i> {{ __('customer.edit') }}
            </a>
        @endcan
    </div>

    <div class="row">
        {{-- Profile --}}
        <div class="col-lg-4 mb-3">
            <div class="card h-100">
                <div class="flex-auto p-2">
                    @if ($customer->getFirstMediaUrl('images'))
                        <div class="text-center mb-4">
                            <img src="{{ $customer->getFirstMediaUrl('images') }}" class="img-max-180 img-fluid img-thumbnail rounded-circle" alt="Customer Image">
                        </div>
                    @endif
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.customer_name') }}</span><span>{{ $customer->customer_name }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.client_type') }}</span><span>{{ __('customer.'.($customer->client_type ?: 'physical_person')) }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.customer_email') }}</span><span>{{ $customer->customer_email }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.customer_phone') }}</span><span>{{ $customer->customer_phone }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.whatsapp_number') }}</span><span>{{ $customer->whatsapp_number ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.responsible_person') }}</span><span>{{ $customer->responsible_person ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.tax_identification_number') }}</span><span>{{ $customer->tax_identification_number ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.iban') }}</span><span>{{ $customer->iban ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.city') }}</span><span>{{ $customer->city }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.country') }}</span><span>{{ $customer->country }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.address') }}</span><span>{{ $customer->address }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.note') }}</span><span>{{ $customer->note ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.document') }}</span><span>@if ($customer->getFirstMediaUrl('documents'))<a href="{{ $customer->getFirstMediaUrl('documents') }}" target="_blank">{{ $customer->getFirstMedia('documents')->file_name }}</a>@else—@endif</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            {{-- Account summary --}}
            <div class="card mb-3">
                <div class="px-3 py-2 bg-slate-50 border-bottom fw-bold">{{ __('customer.account_summary') }}</div>
                <div class="flex-auto p-3">
                    <div class="row text-center g-3">
                        <div class="col-4">
                            <div class="text-slate-500 text-sm">{{ __('customer.total_ordered') }}</div>
                            <div class="fw-bold fs-5">{{ format_currency($totals['total']) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-slate-500 text-sm">{{ __('customer.total_paid') }}</div>
                            <div class="fw-bold fs-5 text-emerald-600">{{ format_currency($totals['paid']) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-slate-500 text-sm">{{ __('customer.total_due') }}</div>
                            <div class="fw-bold fs-5 {{ $totals['due'] > 0 ? 'text-red-600' : '' }}">{{ format_currency($totals['due']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Credit details --}}
            <div class="card mb-3">
                <div class="px-3 py-2 bg-slate-50 border-bottom fw-bold">{{ __('customer.credit_details') }}</div>
                <div class="flex-auto p-2">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.credit_allowed') }}</span>
                            <span>
                                @if ($credit['allowed'])
                                    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700">{{ __('customer.allowed') }}</span>
                                @else
                                    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold bg-slate-200 text-slate-700">{{ __('customer.not_allowed') }}</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customers.credit_limit') }}</span><span>{{ format_currency($credit['limit']) }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customers.current_balance') }}</span>
                            <span class="{{ $credit['over_limit'] ? 'text-red-600 fw-bold' : '' }}">
                                {{ format_currency($credit['balance']) }}
                                @if ($credit['over_limit'])
                                    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold bg-red-100 text-red-700 ms-1">{{ __('customers.over_credit_limit') }}</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customers.available_credit') }}</span><span>{{ format_currency($credit['available']) }}</span></li>
                    </ul>
                </div>
            </div>

            {{-- Orders --}}
            <div class="card mb-3">
                <div class="px-3 py-2 bg-slate-50 border-bottom fw-bold">{{ __('customer.orders') }} <span class="text-slate-500">({{ $sales->count() }})</span></div>
                <div class="flex-auto p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('sales.reference') }}</th>
                                    <th>{{ __('sales.date') }}</th>
                                    <th class="text-end">{{ __('sales.total') }}</th>
                                    <th class="text-end">{{ __('sales.paid') }}</th>
                                    <th class="text-end">{{ __('sales.due') }}</th>
                                    <th>{{ __('sales.status') }}</th>
                                    <th>{{ __('sales.payment_status') }}</th>
                                    <th class="text-end">{{ __('customer.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sales as $sale)
                                    <tr wire:key="cust-sale-{{ $sale->id }}">
                                        <td class="fw-semibold">{{ $sale->reference }}</td>
                                        <td>{{ $sale->date }}</td>
                                        <td class="text-end">{{ format_currency($sale->total_amount) }}</td>
                                        <td class="text-end text-emerald-600">{{ format_currency($sale->paid_amount) }}</td>
                                        <td class="text-end {{ $sale->due_amount > 0 ? 'text-red-600' : '' }}">{{ format_currency($sale->due_amount) }}</td>
                                        <td>@include('sale.partials.status', ['data' => $sale])</td>
                                        <td>@include('sale.partials.payment-status', ['data' => $sale])</td>
                                        <td class="text-end">
                                            <div class="d-flex flex-wrap justify-content-end gap-1">
                                                @can('show_sales')
                                                    <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('customer.details') }}"><i class="bi bi-eye"></i></a>
                                                @endcan
                                                @can('access_sale_payments')
                                                    <a href="{{ route('sale-payments.index', $sale->id) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('customer.show_payments') }}"><i class="bi bi-cash-coin"></i></a>
                                                    @if ($sale->due_amount > 0)
                                                        <a href="{{ route('sale-payments.create', $sale->id) }}" class="btn btn-sm btn-outline-success" title="{{ __('customer.add_payment') }}"><i class="bi bi-plus-circle"></i></a>
                                                    @endif
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-3">{{ __('customer.no_orders') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Payments --}}
            <div class="card mb-3">
                <div class="px-3 py-2 bg-slate-50 border-bottom fw-bold">{{ __('customer.payments') }} <span class="text-slate-500">({{ $payments->count() }})</span></div>
                <div class="flex-auto p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('sales.date') }}</th>
                                    <th>{{ __('sales.reference') }}</th>
                                    <th>{{ __('sales.payment_method') }}</th>
                                    <th class="text-end">{{ __('sales.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $payment)
                                    <tr wire:key="cust-pay-{{ $payment->id }}">
                                        <td>{{ $payment->date }}</td>
                                        <td class="fw-semibold">{{ $payment->reference }}</td>
                                        <td>{{ $payment->payment_method ?: '—' }}</td>
                                        <td class="text-end text-emerald-600">{{ format_currency($payment->amount) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-3">{{ __('customer.no_payments') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
