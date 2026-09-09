{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
<div class="container-fluid">
    {{-- Page-level actions --}}
    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
        <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> {{ __('supplier.back_to_list') }}
        </a>
        @can('create_purchases')
            <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i> {{ __('supplier.new_purchase') }}
            </a>
        @endcan
        @can('update_suppliers')
            <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-pencil me-1"></i> {{ __('supplier.edit_supplier') }}
            </a>
        @endcan
    </div>

    <div class="row">
        {{-- Profile --}}
        <div class="col-lg-4 mb-3">
            <div class="card h-100">
                <div class="flex-auto p-2">
                    @if ($supplier->getFirstMediaUrl('images'))
                        <div class="text-center mb-4">
                            <img src="{{ $supplier->getFirstMediaUrl('images') }}" class="img-max-180 img-fluid img-thumbnail rounded-circle" alt="Supplier Image">
                        </div>
                    @endif
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.supplier_name') }}</span><span>{{ $supplier->supplier_name }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.supplier_email') }}</span><span>{{ $supplier->supplier_email }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.supplier_phone') }}</span><span>{{ $supplier->supplier_phone }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.whatsapp_number') }}</span><span>{{ $supplier->whatsapp_number ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.responsible_person') }}</span><span>{{ $supplier->responsible_person ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.tax_identification_number') }}</span><span>{{ $supplier->tax_identification_number ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.iban') }}</span><span>{{ $supplier->iban ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.city') }}</span><span>{{ $supplier->city }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.country') }}</span><span>{{ $supplier->country }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.address') }}</span><span>{{ $supplier->address }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.note') }}</span><span>{{ $supplier->note ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.document') }}</span><span>@if ($supplier->getFirstMediaUrl('documents'))<a href="{{ $supplier->getFirstMediaUrl('documents') }}" target="_blank">{{ $supplier->getFirstMedia('documents')->file_name }}</a>@else—@endif</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            {{-- Account summary --}}
            <div class="card mb-3">
                <div class="px-3 py-2 bg-slate-50 border-bottom fw-bold">{{ __('supplier.account_summary') }}</div>
                <div class="flex-auto p-3">
                    <div class="row text-center g-3">
                        <div class="col-4">
                            <div class="text-slate-500 text-sm">{{ __('supplier.total_ordered') }}</div>
                            <div class="fw-bold fs-5">{{ format_currency($totals['total']) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-slate-500 text-sm">{{ __('supplier.total_paid') }}</div>
                            <div class="fw-bold fs-5 text-emerald-600">{{ format_currency($totals['paid']) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-slate-500 text-sm">{{ __('supplier.amount_owed') }}</div>
                            <div class="fw-bold fs-5 {{ $totals['due'] > 0 ? 'text-red-600' : '' }}">{{ format_currency($totals['due']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payable details --}}
            <div class="card mb-3">
                <div class="px-3 py-2 bg-slate-50 border-bottom fw-bold">{{ __('supplier.credit_details') }}</div>
                <div class="flex-auto p-2">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.total_ordered') }}</span><span>{{ format_currency($totals['total']) }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.total_paid') }}</span><span class="text-emerald-600">{{ format_currency($totals['paid']) }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.amount_owed') }}</span><span class="{{ $totals['due'] > 0 ? 'text-red-600 fw-bold' : '' }}">{{ format_currency($totals['due']) }}</span></li>
                    </ul>
                </div>
            </div>

            {{-- Orders --}}
            <div class="card mb-3">
                <div class="px-3 py-2 bg-slate-50 border-bottom fw-bold">{{ __('supplier.orders') }} <span class="text-slate-500">({{ $purchases->count() }})</span></div>
                <div class="flex-auto p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('purchase.reference') }}</th>
                                    <th>{{ __('purchase.date') }}</th>
                                    <th class="text-end">{{ __('purchase.total') }}</th>
                                    <th class="text-end">{{ __('purchase.paid') }}</th>
                                    <th class="text-end">{{ __('purchase.due') }}</th>
                                    <th>{{ __('purchase.status') }}</th>
                                    <th>{{ __('purchase.payment_status') }}</th>
                                    <th class="text-end">{{ __('supplier.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchases as $purchase)
                                    <tr wire:key="sup-purchase-{{ $purchase->id }}">
                                        <td class="fw-semibold">{{ $purchase->reference }}</td>
                                        <td>{{ $purchase->date }}</td>
                                        <td class="text-end">{{ format_currency($purchase->total_amount) }}</td>
                                        <td class="text-end text-emerald-600">{{ format_currency($purchase->paid_amount) }}</td>
                                        <td class="text-end {{ $purchase->due_amount > 0 ? 'text-red-600' : '' }}">{{ format_currency($purchase->due_amount) }}</td>
                                        <td>@include('purchase.partials.status', ['data' => $purchase])</td>
                                        <td>@include('purchase.partials.payment-status', ['data' => $purchase])</td>
                                        <td class="text-end">
                                            <div class="d-flex flex-wrap justify-content-end gap-1">
                                                @can('show_purchases')
                                                    <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('supplier.details') }}"><i class="bi bi-eye"></i></a>
                                                @endcan
                                                @can('access_purchase_payments')
                                                    <a href="{{ route('purchase-payments.index', $purchase->id) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('supplier.show_payments') }}"><i class="bi bi-cash-coin"></i></a>
                                                    @if ($purchase->due_amount > 0)
                                                        <a href="{{ route('purchase-payments.create', $purchase->id) }}" class="btn btn-sm btn-outline-success" title="{{ __('supplier.add_payment') }}"><i class="bi bi-plus-circle"></i></a>
                                                    @endif
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-3">{{ __('supplier.no_orders') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Payments --}}
            <div class="card mb-3">
                <div class="px-3 py-2 bg-slate-50 border-bottom fw-bold">{{ __('supplier.payments') }} <span class="text-slate-500">({{ $payments->count() }})</span></div>
                <div class="flex-auto p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('purchase.date') }}</th>
                                    <th>{{ __('purchase.reference') }}</th>
                                    <th>{{ __('purchase.payment_method') }}</th>
                                    <th class="text-end">{{ __('purchase.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $payment)
                                    <tr wire:key="sup-pay-{{ $payment->id }}">
                                        <td>{{ $payment->date }}</td>
                                        <td class="fw-semibold">{{ $payment->reference }}</td>
                                        <td>{{ $payment->payment_method ?: '—' }}</td>
                                        <td class="text-end text-emerald-600">{{ format_currency($payment->amount) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-3">{{ __('supplier.no_payments') }}</td></tr>
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
