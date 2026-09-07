<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <a href="{{ route('sales.create') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                {{ __('sales.add_sale') }} <i class="bi bi-plus"></i>
            </a>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $sales->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($sales as $sale)
            <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 mb-3" wire:key="sale-{{ $sale->id }}">
                <div class="group relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 h-100 overflow-hidden transition-all duration-200 hover:shadow-md hover:border-indigo-300 hover:-translate-y-0.5">
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
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900"><div class="flex-auto p-5 text-center text-muted">{{ __('sales.no_sales_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $sales->links('pagination::bootstrap-5') }}
    </div>
</div>
