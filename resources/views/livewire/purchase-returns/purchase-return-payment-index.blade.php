<div>
    <div class="row">
        <div class="col-12 col-md-6 ml-auto mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="{{ __('app.search') }}">
        </div>
    </div>
    <div class="d-flex justify-content-center mb-3">{{ $payments->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($payments as $payment)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="prp-{{ $payment->id }}">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 h-100">
                    <div class="flex-auto p-5">
                        <h5 class="mb-3 text-lg font-semibold text-slate-900">{{ $payment->reference }}</h5>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.date') }}</span><span>{{ $payment->date }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.amount') }}</span><span>{{ format_currency($payment->amount) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('purchase-returns.payment_method') }}</span><span>{{ $payment->payment_method }}</span></li>
                        </ul>
                        <div class="btn-group">
                            @can('access_purchase_return_payments')
                                <a href="{{ route('purchase-return-payments.edit', [$purchaseReturnId, $payment->id]) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-cyan-500 !text-white border-cyan-500 hover:bg-cyan-600 !px-3 !py-1.5 !text-xs"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-red-500 !text-white border-red-500 hover:bg-red-600 hover:border-red-600 !px-3 !py-1.5 !text-xs" wire:click="delete({{ $payment->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900"><div class="flex-auto p-5 text-center text-muted">{{ __('purchase-returns.no_payments_found') }}</div></div>
            </div>
        @endforelse
    </div>
    <div class="d-flex justify-content-center">{{ $payments->links('pagination::bootstrap-5') }}</div>
</div>
