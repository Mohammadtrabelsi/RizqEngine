<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                {{ __('purchase.add_purchase') }} <i class="bi bi-plus"></i>
            </a>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $purchases->links() }}</div>
    <div class="row">
        @forelse($purchases as $purchase)
            <div class="col-xl-4 col-lg-6 mb-4" wire:key="purchase-{{ $purchase->id }}">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fw-bold">{{ $purchase->reference }}</span>
                        @include('purchase.partials.status', ['data' => $purchase])
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3"><i class="bi bi-truck"></i> {{ $purchase->supplier_name }}</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase.total') }}</span><span>{{ format_currency($purchase->total_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase.paid') }}</span><span>{{ format_currency($purchase->paid_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase.due') }}</span><span>{{ format_currency($purchase->due_amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase.payment_status') }}</span>@include('purchase.partials.payment-status', ['data' => $purchase])</li>
                        </ul>
                    </div>
                    <div class="card-footer text-center">
                        @include('purchase.partials.actions', ['data' => $purchase])
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-muted">{{ __('purchase.no_purchases_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $purchases->links() }}
    </div>
</div>
