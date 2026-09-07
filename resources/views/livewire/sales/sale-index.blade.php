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
            <div class="col-xl-4 col-lg-6 mb-4" wire:key="sale-{{ $sale->id }}">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 h-100">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
                        <span class="fw-bold">{{ $sale->reference }}</span>
                        @include('sale.partials.status', ['data' => $sale])
                    </div>
                    <div class="flex-auto p-5">
                        <h6 class="mb-3"><i class="bi bi-person"></i> {{ $sale->customer_name }}</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.total') }}</span><span>{{ format_currency($sale->total_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.paid') }}</span><span>{{ format_currency($sale->paid_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.due') }}</span><span>{{ format_currency($sale->due_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.payment_status') }}</span>@include('sale.partials.payment-status', ['data' => $sale])</li>
                        </ul>
                    </div>
                    <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-200 text-center">
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
