<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <a href="{{ route('purchase-returns.create') }}" class="btn btn-primary">
                {{ __('purchase-returns.add_return') }} <i class="bi bi-plus"></i>
            </a>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    {{-- Filters container --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
            <h5 class="mb-0"><i class="bi bi-funnel text-primary"></i> {{ __('app.filters') }}</h5>
        </div>
        <div class="flex-auto p-2">
            <div class="row align-items-end">
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('purchase-returns.status') }}</label>
                    <select wire:model.live="status" class="form-select">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="Pending">{{ __('purchase-returns.pending') }}</option>
                        <option value="Shipped">{{ __('purchase-returns.shipped') }}</option>
                        <option value="Completed">{{ __('purchase-returns.completed') }}</option>
                    </select>
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('purchase-returns.payment_status') }}</label>
                    <select wire:model.live="paymentStatus" class="form-select">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="Unpaid">Unpaid</option>
                        <option value="Partial">Partial</option>
                        <option value="Paid">{{ __('purchase-returns.paid') }}</option>
                    </select>
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('reports.start_date') }}</label>
                    <input type="date" wire:model.live="dateFrom" class="form-control">
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('reports.end_date') }}</label>
                    <input type="date" wire:model.live="dateTo" class="form-control">
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <button type="button" wire:click="resetFilters" class="btn btn-secondary w-100">
                        <i class="bi bi-x-circle"></i> {{ __('app.reset') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $purchase_returns->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($purchase_returns as $purchase_return)
            <div class="col-xl-4 col-lg-6 mb-4" wire:key="purchase-return-{{ $purchase_return->id }}">
                <div class="card h-100">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
                        <span class="fw-bold">{{ $purchase_return->reference }}</span>
                        @include('purchasesreturn.partials.status', ['data' => $purchase_return])
                    </div>
                    <div class="flex-auto p-2">
                        <h6 class="mb-3"><i class="bi bi-truck"></i> {{ $purchase_return->supplier_name }}</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.total') }}</span><span>{{ format_currency($purchase_return->total_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.paid') }}</span><span>{{ format_currency($purchase_return->paid_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.due') }}</span><span>{{ format_currency($purchase_return->due_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.payment_status') }}</span>@include('purchasesreturn.partials.payment-status', ['data' => $purchase_return])</li>
                        </ul>
                    </div>
                    <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-200 text-center">
                        @include('purchasesreturn.partials.actions', ['data' => $purchase_return])
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('purchase-returns.no_returns_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $purchase_returns->links('pagination::bootstrap-5') }}
    </div>
</div>
