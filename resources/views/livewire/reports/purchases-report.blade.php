<div>
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form wire:submit="generateReport">
                        <div class="form-row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('report.start-date') }} <span class="text-danger">*</span></label>
                                    <input wire:model="start_date" type="date" class="form-control" name="start_date">
                                    @error('start_date')
                                    <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('report.end-date') }} <span class="text-danger">*</span></label>
                                    <input wire:model="end_date" type="date" class="form-control" name="end_date">
                                    @error('end_date')
                                    <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('report.supplier') }}</label>
                                    <select wire:model="supplier_id" class="form-control" name="supplier_id">
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
                                <div class="form-group">
                                    <label>{{ __('report.status') }}</label>
                                    <select wire:model="purchase_status" class="form-control" name="purchase_status">
                                        <option value="">{{ __('report.select-status') }}</option>
                                        <option value="Pending">{{ __('report.pending') }}</option>
                                        <option value="Ordered">{{ __('report.ordered') }}</option>
                                        <option value="Completed">{{ __('report.completed') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('report.payment-status') }}</label>
                                    <select wire:model="payment_status" class="form-control" name="payment_status">
                                        <option value="">{{ __('report.select-payment-status') }}</option>
                                        <option value="Paid">{{ __('report.paid') }}</option>
                                        <option value="Unpaid">{{ __('report.unpaid') }}</option>
                                        <option value="Partial">{{ __('report.partial') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
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
            <div class="card border-0 shadow-sm">
                <div class="card-body">
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
                                                <span class="badge badge-info">{{ $purchase->status }}</span>
                                            @elseif ($purchase->status == 'Ordered')
                                                <span class="badge badge-primary">{{ $purchase->status }}</span>
                                            @else
                                                <span class="badge badge-success">{{ $purchase->status }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($purchase->payment_status == 'Partial')
                                                <span class="badge badge-warning">{{ $purchase->payment_status }}</span>
                                            @elseif ($purchase->payment_status == 'Paid')
                                                <span class="badge badge-success">{{ $purchase->payment_status }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ $purchase->payment_status }}</span>
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
