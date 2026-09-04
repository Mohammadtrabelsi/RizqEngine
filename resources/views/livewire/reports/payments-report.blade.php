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
        <div class="row mb-2">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-tile-48 rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3">
                            <i class="bi bi-collection fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase">{{ __('reports.payments') }}</div>
                            <div class="h4 mb-0 fw-bold">{{ number_format($summary['count']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-tile-48 rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase">{{ __('report.total') }}</div>
                            <div class="h4 mb-0 fw-bold">{{ format_currency($summary['total_amount']) }}</div>
                        </div>
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
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small text-uppercase">
                                        <th scope="col">{{ __('report.reference') }}</th>
                                        <th scope="col">{{ __('report.date') }}</th>
                                        <th scope="col">{{ ucwords(str_replace('_', ' ', $payments)) }}</th>
                                        <th scope="col">{{ __('reports.payment_method') }}</th>
                                        <th scope="col" class="text-end">{{ __('report.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($information as $data)
                                        <tr>
                                            <td class="fw-bold">{{ $data->reference }}</td>
                                            <td>{{ \Carbon\Carbon::parse($data->date)->format('d M, Y') }}</td>
                                            <td>
                                                @if($payments == 'sale')
                                                    {{ $data->sale->reference }}
                                                @elseif($payments == 'purchase')
                                                    {{ $data->purchase->reference }}
                                                @elseif($payments == 'sale_return')
                                                    {{ $data->saleReturn->reference }}
                                                @elseif($payments == 'purchase_return')
                                                    {{ $data->purchaseReturn->reference }}
                                                @endif
                                            </td>
                                            <td>{{ $data->payment_method }}</td>
                                            <td class="text-end">{{ format_currency($data->amount) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">{{ __('report.no-data-available') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div @class(['mt-3' => $information->hasPages()])>
                            {{ $information->links('pagination::bootstrap-5') }}
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
