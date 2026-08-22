<div>
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form wire:submit="generateReport">
                        <div class="form-row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('reports.start_date') }} <span class="text-danger">*</span></label>
                                    <input wire:model="start_date" type="date" class="form-control" name="start_date">
                                    @error('start_date')
                                    <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('reports.end_date') }} <span class="text-danger">*</span></label>
                                    <input wire:model="end_date" type="date" class="form-control" name="end_date">
                                    @error('end_date')
                                    <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('reports.payments') }}</label>
                                    <select wire:model.live="payments" class="form-control" name="payments">
                                        <option value="">{{ __('reports.select_payments') }}</option>
                                        <option value="sale">Sales</option>
                                        <option value="sale_return">Sale Returns</option>
                                        <option value="purchase">Purchase</option>
                                        <option value="purchase_return">Purchase Returns</option>
                                    </select>
                                    @error('payments')
                                    <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('reports.payment_method') }}</label>
                                    <select wire:model="payment_method" class="form-control" name="payment_method">
                                        <option value="">{{ __('reports.select_payment_method') }}</option>
                                        <option value="Cash">{{ __('reports.cash') }}</option>
                                        <option value="Credit Card">{{ __('reports.credit_card') }}</option>
                                        <option value="Bank Transfer">{{ __('reports.bank_transfer') }}</option>
                                        <option value="Cheque">{{ __('reports.cheque') }}</option>
                                        <option value="Other">{{ __('reports.other') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <span wire:target="generateReport" wire:loading class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <i wire:target="generateReport" wire:loading.remove class="bi bi-shuffle"></i>
                                {{ __('reports.filter_report') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($information->isNotEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div wire:loading.flex class="col-12 position-absolute justify-content-center align-items-center wire-loading-overlay">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mb-3">{{ $information->links() }}</div>
                        <div class="row">
                            @forelse($information as $data)
                                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $data->reference }}</h5>
                                            <ul class="list-group list-group-flush mb-0">
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>Date</span><span>{{ \Carbon\Carbon::parse($data->date)->format('d M, Y') }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0">
                                                    <span>{{ ucwords(str_replace('_', ' ', $payments)) }}</span>
                                                    <span>
                                                        @if($payments == 'sale')
                                                            {{ $data->sale->reference }}
                                                        @elseif($payments == 'purchase')
                                                            {{ $data->purchase->reference }}
                                                        @elseif($payments == 'sale_return')
                                                            {{ $data->saleReturn->reference }}
                                                        @elseif($payments == 'purchase_return')
                                                            {{ $data->purchaseReturn->reference }}
                                                        @endif
                                                    </span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>Total</span><span>{{ format_currency($data->amount) }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>Payment Method</span><span>{{ $data->payment_method }}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12"><span class="text-danger">No Data Available!</span></div>
                            @endforelse
                        </div>
                        <div @class(['mt-3' => $information->hasPages()])>
                            {{ $information->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="alert alert-warning mb-0">
                            {{ __('report.no-data-available') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
