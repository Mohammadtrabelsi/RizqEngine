<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <a href="{{ route('sales.create') }}" class="btn btn-primary">
                {{ __('sales.add_sale') }} <i class="bi bi-plus"></i>
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
                    <label class="form-label small text-muted mb-1">{{ __('sales.status') }}</label>
                    <select wire:model.live="status" class="form-select">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="Pending">{{ __('sales.pending') }}</option>
                        <option value="Shipped">{{ __('sales.shipped') }}</option>
                        <option value="Completed">{{ __('sales.completed') }}</option>
                    </select>
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('sales.payment_status') }}</label>
                    <select wire:model.live="paymentStatus" class="form-select">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="Unpaid">Unpaid</option>
                        <option value="Partial">Partial</option>
                        <option value="Paid">{{ __('sales.paid') }}</option>
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

    <div class="d-flex justify-content-center mb-3">{{ $sales->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($sales as $sale)
            <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 mb-3" wire:key="sale-{{ $sale->id }}">
                <div class="group card h-100 overflow-hidden transition-all duration-200 hover:shadow-md hover:border-indigo-300 hover:-translate-y-0.5">
                    <span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 to-violet-500 opacity-0 transition-opacity duration-200 group-hover:opacity-100"></span>
                    <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-sm tracking-tight">{{ $sale->reference }}</span>
                        @include('sale.partials.status', ['data' => $sale])
                    </div>
                    <div class="flex-auto px-4 py-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex-shrink-0"><i class="bi bi-person text-sm"></i></span>
                            <span class="fw-semibold text-sm text-truncate">{{ $sale->customer_name }}</span>
                        </div>
                        <dl class="mb-0 text-sm">
                            <div class="d-flex justify-content-between py-1.5 border-bottom border-slate-100"><dt class="fw-normal text-slate-500">{{ __('sales.total') }}</dt><dd class="mb-0 fw-semibold">{{ format_currency($sale->total_amount) }}</dd></div>
                            <div class="d-flex justify-content-between py-1.5 border-bottom border-slate-100"><dt class="fw-normal text-slate-500">{{ __('sales.paid') }}</dt><dd class="mb-0 fw-semibold text-emerald-600">{{ format_currency($sale->paid_amount) }}</dd></div>
                            <div class="d-flex justify-content-between py-1.5 border-bottom border-slate-100"><dt class="fw-normal text-slate-500">{{ __('sales.due') }}</dt><dd class="mb-0 fw-semibold {{ $sale->due_amount > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ format_currency($sale->due_amount) }}</dd></div>
                            <div class="d-flex justify-content-between align-items-center pt-1.5"><dt class="fw-normal text-slate-500">{{ __('sales.payment_status') }}</dt><dd class="mb-0">@include('sale.partials.payment-status', ['data' => $sale])</dd></div>
                        </dl>
                    </div>
                    <div class="px-4 py-2 bg-slate-50 border-t border-slate-200 text-center">
                        @include('sale.partials.actions', ['data' => $sale])
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('sales.no_sales_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $sales->links('pagination::bootstrap-5') }}
    </div>
</div>
