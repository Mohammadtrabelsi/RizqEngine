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

    <div class="row">
        @forelse($purchase_returns as $purchase_return)
            <div class="col-xl-4 col-lg-6 mb-4" wire:key="purchase-return-{{ $purchase_return->id }}">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fw-bold">{{ $purchase_return->reference }}</span>
                        @include('purchasesreturn.partials.status', ['data' => $purchase_return])
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3"><i class="bi bi-truck"></i> {{ $purchase_return->supplier_name }}</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.total') }}</span><span>{{ format_currency($purchase_return->total_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.paid') }}</span><span>{{ format_currency($purchase_return->paid_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.due') }}</span><span>{{ format_currency($purchase_return->due_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.payment_status') }}</span>@include('purchasesreturn.partials.payment-status', ['data' => $purchase_return])</li>
                        </ul>
                    </div>
                    <div class="card-footer text-center">
                        @include('purchasesreturn.partials.actions', ['data' => $purchase_return])
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-muted">{{ __('purchase-returns.no_returns_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $purchase_returns->links() }}
    </div>
</div>
