<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <a href="{{ route('sale-returns.create') }}" class="btn btn-primary">
                {{ __('sales.add_sale_return') }} <i class="bi bi-plus"></i>
            </a>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $sale_returns->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($sale_returns as $sale_return)
            <div class="col-xl-4 col-lg-6 mb-4" wire:key="sale-return-{{ $sale_return->id }}">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fw-bold">{{ $sale_return->reference }}</span>
                        @include('salesreturn.partials.status', ['data' => $sale_return])
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3"><i class="bi bi-person"></i> {{ $sale_return->customer_name }}</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.total') }}</span><span>{{ format_currency($sale_return->total_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.paid') }}</span><span>{{ format_currency($sale_return->paid_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.due') }}</span><span>{{ format_currency($sale_return->due_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('sales.payment_status') }}</span>@include('salesreturn.partials.payment-status', ['data' => $sale_return])</li>
                        </ul>
                    </div>
                    <div class="card-footer text-center">
                        @include('salesreturn.partials.actions', ['data' => $sale_return])
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-muted">{{ __('sales.no_sale_returns_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $sale_returns->links('pagination::bootstrap-5') }}
    </div>
</div>
