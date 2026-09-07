<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                <div class="flex-auto p-5">
                    <form wire:submit="generateReport">
                        <div class="form-row">
                            <div class="col-lg-4">
                                <div class="mb-4">
                                    <label>{{ __('report.start-date') }} <span class="text-danger">*</span></label>
                                    <input wire:model="start_date" type="date" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="start_date">
                                    @error('start_date')
                                    <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-4">
                                    <label>{{ __('report.end-date') }} <span class="text-danger">*</span></label>
                                    <input wire:model="end_date" type="date" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="end_date">
                                    @error('end_date')
                                    <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-4">
                                    <label>{{ __('report.supplier') }}</label>
                                    <select wire:model="supplier_id" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="supplier_id">
                                        <option value="">{{ __('report.select-supplier') }}</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label>{{ __('report.status') }}</label>
                                    <select wire:model="purchase_status" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="purchase_status">
                                        <option value="">{{ __('report.select-status') }}</option>
                                        <option value="Pending">{{ __('report.pending') }}</option>
                                        <option value="Ordered">{{ __('report.ordered') }}</option>
                                        <option value="Completed">{{ __('report.completed') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label>{{ __('report.payment-status') }}</label>
                                    <select wire:model="payment_status" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="payment_status">
                                        <option value="">{{ __('report.select-payment-status') }}</option>
                                        <option value="Paid">{{ __('report.paid') }}</option>
                                        <option value="Unpaid">{{ __('report.unpaid') }}</option>
                                        <option value="Partial">{{ __('report.partial') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4 mb-0">
                            <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                                <span wire:target="generateReport" wire:loading class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <i wire:target="generateReport" wire:loading.remove class="bi bi-shuffle"></i>
                                {{ __('report.filter-report') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('livewire.reports.partials.summary-cards', ['countLabel' => __('report.purchases')])

    <div class="row">
        <div class="col-12">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                <div class="flex-auto p-5">
                    <div wire:loading.flex class="col-12 position-absolute justify-content-center align-items-center wire-loading-overlay">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">{{ __('report.loading') }}</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
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
                                @forelse($purchases as $purchase)
                                    <tr>
                                        <td class="fw-bold">{{ $purchase->reference }}</td>
                                        <td><i class="bi bi-truck text-muted"></i> {{ $purchase->supplier_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($purchase->date)->format('d M, Y') }}</td>
                                        <td class="text-end">{{ format_currency($purchase->total_amount) }}</td>
                                        <td class="text-end text-success">{{ format_currency($purchase->paid_amount) }}</td>
                                        <td class="text-end @if($purchase->due_amount > 0) text-danger fw-bold @else text-muted @endif">{{ format_currency($purchase->due_amount) }}</td>
                                        <td class="text-center">
                                            @if ($purchase->status == 'Pending')
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-cyan-100 text-cyan-700">{{ $purchase->status }}</span>
                                            @elseif ($purchase->status == 'Ordered')
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-indigo-100 text-indigo-700">{{ $purchase->status }}</span>
                                            @else
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">{{ $purchase->status }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($purchase->payment_status == 'Partial')
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-amber-100 text-amber-700">{{ $purchase->payment_status }}</span>
                                            @elseif ($purchase->payment_status == 'Paid')
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">{{ $purchase->payment_status }}</span>
                                            @else
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-red-100 text-red-700">{{ $purchase->payment_status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">{{ __('report.no-purchases-data') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div @class(['mt-3' => $purchases->hasPages()])>
                        {{ $purchases->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
