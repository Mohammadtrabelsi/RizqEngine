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

    <div class="row">
        @forelse($sales as $sale)
            <div class="col-xl-4 col-lg-6 mb-4" wire:key="sale-{{ $sale->id }}">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fw-bold">{{ $sale->reference }}</span>
                        @include('sale.partials.status', ['data' => $sale])
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3"><i class="bi bi-person"></i> {{ $sale->customer_name }}</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.total') }}</span><span>{{ format_currency($sale->total_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.paid') }}</span><span>{{ format_currency($sale->paid_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.due') }}</span><span>{{ format_currency($sale->due_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.payment_status') }}</span>@include('sale.partials.payment-status', ['data' => $sale])</li>
                        </ul>
                    </div>
                    <div class="card-footer text-center">
                        @include('sale.partials.actions', ['data' => $sale])
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-muted">{{ __('sales.no_sales_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $sales->links() }}
    </div>
</div>
