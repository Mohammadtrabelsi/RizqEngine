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
                                    <select wire:model="purchase_return_status" class="form-control" name="purchase_return_status">
                                        <option value="">{{ __('report.select-status') }}</option>
                                        <option value="Pending">{{ __('report.pending') }}</option>
                                        <option value="Shipped">{{ __('report.shipped') }}</option>
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
                                Filter Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div wire:loading.flex class="col-12 position-absolute justify-content-center align-items-center wire-loading-overlay">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">{{ __('report.loading') }}</span>
                        </div>
                    </div>
                    <div class="row">
                        @forelse($purchase_returns as $purchase_return)
                            <div class="col-xl-4 col-lg-6 mb-4">
                                <div class="card border h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">{{ $purchase_return->reference }}</span>
                                        @if ($purchase_return->status == 'Pending')
                                            <span class="badge badge-info">{{ $purchase_return->status }}</span>
                                        @elseif ($purchase_return->status == 'Shipped')
                                            <span class="badge badge-primary">{{ $purchase_return->status }}</span>
                                        @else
                                            <span class="badge badge-success">{{ $purchase_return->status }}</span>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-2"><i class="bi bi-truck"></i> {{ $purchase_return->supplier_name }}</p>
                                        <ul class="list-group list-group-flush mb-0">
                                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.date') }}</span><span>{{ \Carbon\Carbon::parse($purchase_return->date)->format('d M, Y') }}</span></li>
                                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.total') }}</span><span>{{ format_currency($purchase_return->total_amount) }}</span></li>
                                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.paid') }}</span><span>{{ format_currency($purchase_return->paid_amount) }}</span></li>
                                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.due') }}</span><span>{{ format_currency($purchase_return->due_amount) }}</span></li>
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span>{{ __('report.payment-status') }}</span>
                                                @if ($purchase_return->payment_status == 'Partial')
                                                    <span class="badge badge-warning">{{ $purchase_return->payment_status }}</span>
                                                @elseif ($purchase_return->payment_status == 'Paid')
                                                    <span class="badge badge-success">{{ $purchase_return->payment_status }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ $purchase_return->payment_status }}</span>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12"><span class="text-danger">{{ __('report.no-purchase-returns-data') }}</span></div>
                        @endforelse
                    </div>
                    <div @class(['mt-3' => $purchase_returns->hasPages()])>
                        {{ $purchase_returns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
