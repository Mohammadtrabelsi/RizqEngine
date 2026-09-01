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
                                    <label>{{ __('report.customer') }}</label>
                                    <select wire:model="customer_id" class="form-control" name="customer_id">
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('report.status') }}</label>
                                    <select wire:model="sale_status" class="form-control" name="sale_status">
                                        <option value="">Select Status</option>
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
                                        <option value="">Select Payment Status</option>
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

    @include('livewire.reports.partials.summary-cards', ['countLabel' => __('report.sales')])

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div wire:loading.flex class="col-12 position-absolute justify-content-center align-items-center wire-loading-overlay">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">{{ __('report.loading') }}</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mb-3">{{ $sales->links('pagination::bootstrap-5') }}</div>
                    <div class="row">
                        @forelse($sales as $sale)
                            <div class="col-xl-4 col-lg-6 mb-4">
                                <div class="card border h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">{{ $sale->reference }}</span>
                                        @if ($sale->status == 'Pending')
                                            <span class="badge badge-info">{{ $sale->status }}</span>
                                        @elseif ($sale->status == 'Shipped')
                                            <span class="badge badge-primary">{{ $sale->status }}</span>
                                        @else
                                            <span class="badge badge-success">{{ $sale->status }}</span>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-2"><i class="bi bi-person"></i> {{ $sale->customer_name }}</p>
                                        <ul class="list-group list-group-flush mb-0">
                                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.date') }}</span><span>{{ \Carbon\Carbon::parse($sale->date)->format('d M, Y') }}</span></li>
                                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.total') }}</span><span>{{ format_currency($sale->total_amount) }}</span></li>
                                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.paid') }}</span><span>{{ format_currency($sale->paid_amount) }}</span></li>
                                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.due') }}</span><span>{{ format_currency($sale->due_amount) }}</span></li>
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span>{{ __('report.payment-status') }}</span>
                                                @if ($sale->payment_status == 'Partial')
                                                    <span class="badge badge-warning">{{ $sale->payment_status }}</span>
                                                @elseif ($sale->payment_status == 'Paid')
                                                    <span class="badge badge-success">{{ $sale->payment_status }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ $sale->payment_status }}</span>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12"><span class="text-danger">{{ __('report.no-sales-data') }}</span></div>
                        @endforelse
                    </div>
                    <div @class(['mt-3' => $sales->hasPages()])>
                        {{ $sales->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
