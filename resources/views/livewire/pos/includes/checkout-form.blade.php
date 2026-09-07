<div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm mt-3" id="checkoutForm">
    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-cart-check text-primary"></i> {{ __('sale.checkout') }}
        </h5>
        <button type="button" class="close" wire:click="hideCheckout" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form id="checkout-form" action="{{ route('app.pos.store') }}" method="POST">
        @csrf
        <div class="flex-auto p-5">
            @if (session()->has('checkout_message'))
                <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-emerald-50 text-emerald-700 border-emerald-200 pr-12 fade show" role="alert">
                    <div class="alert-body">
                        <span>{{ session('checkout_message') }}</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-lg-7">
                    <input type="hidden" value="{{ $customer_id }}" name="customer_id">
                    <input type="hidden" value="{{ $global_tax }}" name="tax_percentage">
                    <input type="hidden" value="{{ $global_discount }}" name="discount_percentage">
                    <input type="hidden" value="{{ $shipping }}" name="shipping_amount">
                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label for="total_amount">{{ __('sale.total-amount') }} <span class="text-danger">*</span></label>
                                <input id="total_amount" type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="total_amount" value="{{ $total_amount }}" readonly required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label for="paid_amount">{{ __('sale.received-amount') }} <span class="text-danger">*</span></label>
                                <input id="paid_amount" type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="paid_amount" value="{{ $total_amount }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="payment_method">{{ __('sale.payment-method') }} <span class="text-danger">*</span></label>
                        <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="payment_method" id="payment_method" required>
                            <option value="Cash">{{ __('sale.cash') }}</option>
                            <option value="Credit Card">{{ __('sale.credit-card') }}</option>
                            <option value="Bank Transfer">{{ __('sale.bank-transfer') }}</option>
                            <option value="Cheque">{{ __('sale.cheque') }}</option>
                            <option value="Other">{{ __('sale.other') }}</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="note">{{ __('sale.note') }}</label>
                        <textarea name="note" id="note" rows="5" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45"></textarea>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="block w-full overflow-x-auto">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('sale.total-products') }}</span>
                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">{{ Cart::instance($cart_instance)->count() }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('sale.order-tax') }} ({{ $global_tax }}%)</span><span>(+) {{ format_currency(Cart::instance($cart_instance)->tax()) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('sale.discount') }} ({{ $global_discount }}%)</span><span>(-) {{ format_currency(Cart::instance($cart_instance)->discount()) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('sale.shipping') }}</span>
                                <span>
                                    <input type="hidden" value="{{ $shipping }}" name="shipping_amount">
                                    (+) {{ format_currency($shipping) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between text-primary">
                                <span class="fw-bold">{{ __('sale.grand-total') }}</span><span class="fw-bold">(=) {{ format_currency($total_with_shipping) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-200 d-flex justify-content-end">
            <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-white !text-slate-700 border-slate-300 hover:bg-slate-50 hover:!text-slate-900 hover:border-slate-400 mr-2" wire:click="hideCheckout">{{ __('product.close') }}</button>
            <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">{{ __('product.submit') }}</button>
        </div>
    </form>
</div>
