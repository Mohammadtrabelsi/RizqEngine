<div>
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form wire:submit="generateReport">
                        <div class="form-row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('sale-return.start_date') }} <span class="text-danger">*</span></label>
                                    <input wire:model="start_date" type="date" class="form-control" name="start_date">
                                    @error('start_date')
                                    <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('sale-return.end_date') }} <span class="text-danger">*</span></label>
                                    <input wire:model="end_date" type="date" class="form-control" name="end_date">
                                    @error('end_date')
                                    <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('sale-return.customer') }}</label>
                                    <select wire:model="customer_id" class="form-control" name="customer_id">
                                        <option value="">{{ __('sale-return.select_customer') }}</option>
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
                                    <label>{{ __('sale-return.status') }}</label>
                                    <select wire:model="sale_return_status" class="form-control" name="sale_return_status">
                                        <option value="">{{ __('sale-return.select_status') }}</option>
                                        <option value="Pending">{{ __('sale-return.pending') }}</option>
                                        <option value="Shipped">{{ __('sale-return.shipped') }}</option>
                                        <option value="Completed">{{ __('sale-return.completed') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('sale-return.payment_status') }}</label>
                                    <select wire:model="payment_status" class="form-control" name="payment_status">
                                        <option value="">{{ __('sale-return.select_payment_status') }}</option>
                                        <option value="Paid">{{ __('sale-return.paid') }}</option>
                                        <option value="Unpaid">{{ __('sale-return.unpaid') }}</option>
                                        <option value="Partial">{{ __('sale-return.partial') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <span wire:target="generateReport" wire:loading class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <i wire:target="generateReport" wire:loading.remove class="bi bi-shuffle"></i>
                                {{ __('sale-return.filter_report') }}
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
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mb-3">{{ $sale_returns->links() }}</div>
                    <div class="row">
                        @forelse($sale_returns as $sale_return)
                            <div class="col-xl-4 col-lg-6 mb-4">
                                <div class="card border h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">{{ $sale_return->reference }}</span>
                                        @if ($sale_return->status == 'Pending')
                                            <span class="badge badge-info">{{ $sale_return->status }}</span>
                                        @elseif ($sale_return->status == 'Shipped')
                                            <span class="badge badge-primary">{{ $sale_return->status }}</span>
                                        @else
                                            <span class="badge badge-success">{{ $sale_return->status }}</span>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-2"><i class="bi bi-person"></i> {{ $sale_return->customer_name }}</p>
                                        <ul class="list-group list-group-flush mb-0">
                                            <li class="list-group-item d-flex justify-content-between px-0"><span>Date</span><span>{{ \Carbon\Carbon::parse($sale_return->date)->format('d M, Y') }}</span></li>
                                            <li class="list-group-item d-flex justify-content-between px-0"><span>Total</span><span>{{ format_currency($sale_return->total_amount) }}</span></li>
                                            <li class="list-group-item d-flex justify-content-between px-0"><span>Paid</span><span>{{ format_currency($sale_return->paid_amount) }}</span></li>
                                            <li class="list-group-item d-flex justify-content-between px-0"><span>Due</span><span>{{ format_currency($sale_return->due_amount) }}</span></li>
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span>Payment Status</span>
                                                @if ($sale_return->payment_status == 'Partial')
                                                    <span class="badge badge-warning">{{ $sale_return->payment_status }}</span>
                                                @elseif ($sale_return->payment_status == 'Paid')
                                                    <span class="badge badge-success">{{ $sale_return->payment_status }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ $sale_return->payment_status }}</span>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12"><span class="text-danger">{{ __('report.no-sale-returns-data') }}</span></div>
                        @endforelse
                    </div>
                    <div @class(['mt-3' => $sale_returns->hasPages()])>
                        {{ $sale_returns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
