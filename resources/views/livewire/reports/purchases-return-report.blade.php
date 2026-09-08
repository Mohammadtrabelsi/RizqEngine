<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="flex-auto p-2">
                    <form wire:submit="generateReport">
                        <div class="form-row align-items-end">
                            <div class="col-lg-2 col-md-4 mb-3">
                                <label>{{ __('report.start-date') }} <span class="text-danger">*</span></label>
                                <input wire:model="start_date" type="date" class="form-control" name="start_date">
                                @error('start_date')
                                <span class="text-danger mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-2 col-md-4 mb-3">
                                <label>{{ __('report.end-date') }} <span class="text-danger">*</span></label>
                                <input wire:model="end_date" type="date" class="form-control" name="end_date">
                                @error('end_date')
                                <span class="text-danger mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-2 col-md-4 mb-3">
                                <label>{{ __('report.supplier') }}</label>
                                <select wire:model="supplier_id" class="form-control" name="supplier_id">
                                    <option value="">{{ __('report.select-supplier') }}</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-4 mb-3">
                                <label>{{ __('report.status') }}</label>
                                <select wire:model="purchase_return_status" class="form-control" name="purchase_return_status">
                                    <option value="">{{ __('report.select-status') }}</option>
                                    <option value="Pending">{{ __('report.pending') }}</option>
                                    <option value="Shipped">{{ __('report.shipped') }}</option>
                                    <option value="Completed">{{ __('report.completed') }}</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-4 mb-3">
                                <label>{{ __('report.payment-status') }}</label>
                                <select wire:model="payment_status" class="form-control" name="payment_status">
                                    <option value="">{{ __('report.select-payment-status') }}</option>
                                    <option value="Paid">{{ __('report.paid') }}</option>
                                    <option value="Unpaid">{{ __('report.unpaid') }}</option>
                                    <option value="Partial">{{ __('report.partial') }}</option>
                                </select>
                            </div>
                            <div class="col-auto mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <span wire:target="generateReport" wire:loading class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <i wire:target="generateReport" wire:loading.remove class="bi bi-shuffle"></i>
                                    Filter Report
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('livewire.reports.partials.summary-cards', ['countLabel' => __('report.purchase-returns')])

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="flex-auto p-2">
                    <div wire:loading.flex class="col-12 position-absolute justify-content-center align-items-center wire-loading-overlay">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">{{ __('report.loading') }}</span>
                        </div>
                    </div>
                    <div class="block w-full overflow-x-auto">
                        <table class="w-full mb-4 text-slate-900 border-collapse [&_tbody_tr:hover]:bg-slate-50 align-middle mb-0">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th scope="col">{{ __('report.reference') }}</th>
                                    <th scope="col">{{ __('report.supplier') }}</th>
                                    <th scope="col">{{ __('report.date') }}</th>
                                    <th scope="col" class="text-end">{{ __('report.total') }}</th>
                                    <th scope="col" class="text-end">{{ __('report.paid') }}</th>
                                    <th scope="col" class="text-end">{{ __('report.due') }}</th>
                                    <th scope="col" class="text-center">{{ __('report.status') }}</th>
                                    <th scope="col" class="text-center">{{ __('report.payment-status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchase_returns as $purchase_return)
                                    <tr>
                                        <td class="fw-bold">{{ $purchase_return->reference }}</td>
                                        <td><i class="bi bi-truck text-muted"></i> {{ $purchase_return->supplier_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($purchase_return->date)->format('d M, Y') }}</td>
                                        <td class="text-end">{{ format_currency($purchase_return->total_amount) }}</td>
                                        <td class="text-end text-success">{{ format_currency($purchase_return->paid_amount) }}</td>
                                        <td class="text-end @if($purchase_return->due_amount > 0) text-danger fw-bold @else text-muted @endif">{{ format_currency($purchase_return->due_amount) }}</td>
                                        <td class="text-center">
                                            @if ($purchase_return->status == 'Pending')
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-cyan-100 text-cyan-700">{{ $purchase_return->status }}</span>
                                            @elseif ($purchase_return->status == 'Shipped')
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-indigo-100 text-indigo-700">{{ $purchase_return->status }}</span>
                                            @else
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">{{ $purchase_return->status }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($purchase_return->payment_status == 'Partial')
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-amber-100 text-amber-700">{{ $purchase_return->payment_status }}</span>
                                            @elseif ($purchase_return->payment_status == 'Paid')
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">{{ $purchase_return->payment_status }}</span>
                                            @else
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-red-100 text-red-700">{{ $purchase_return->payment_status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">{{ __('report.no-purchase-returns-data') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div @class(['mt-3' => $purchase_returns->hasPages()])>
                        {{ $purchase_returns->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
