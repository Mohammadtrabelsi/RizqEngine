<div>
    <div class="row">
        <div class="col-12 col-md-6 ml-auto mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>
    <div class="d-flex justify-content-center mb-3">{{ $payments->links() }}</div>
    <div class="row">
        @forelse($payments as $payment)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="pp-{{ $payment->id }}">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $payment->reference }}</h5>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase.date') }}</span><span>{{ $payment->date }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase.amount') }}</span><span>{{ format_currency($payment->amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase.method') }}</span><span>{{ $payment->payment_method }}</span></li>
                        </ul>
                        <div class="btn-group">
                            @can('access_purchase_payments')
                                <a href="{{ route('purchase-payments.edit', [$purchaseId, $payment->id]) }}" class="btn btn-info btn-sm"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $payment->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-muted">{{ __('purchase.no_payments_found') }}</div></div>
            </div>
        @endforelse
    </div>
    <div class="d-flex justify-content-center">{{ $payments->links() }}</div>
</div>
