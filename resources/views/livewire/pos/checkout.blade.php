<div>
    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
        <div class="flex-auto p-5">
            <div>
                @if (session()->has('message'))
                    <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-amber-50 text-amber-700 border-amber-200 pr-12 fade show" role="alert">
                        <div class="alert-body">
                            <span>{{ session('message') }}</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    </div>
                @endif

                <div class="mb-4">
                    <label for="customer_id">{{ __('sale.customer') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <a href="{{ route('customers.create') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                                <i class="bi bi-person-plus"></i>
                            </a>
                        </div>
                        <select wire:model.live="customer_id" id="customer_id" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                            <option value="" selected>{{ __('sale.select-customer') }}</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 mb-3">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                        <h5 class="mb-0">{{ __('general.products') }}</h5>
                    </div>
                    <div class="flex-auto p-5">
                        <div class="row">
                            @if($cart_items->isNotEmpty())
                                @foreach($cart_items as $cart_item)
                                    <div class="col-lg-12 mb-3" wire:key="cart-item-{{ $cart_item->rowId }}">
                                        <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 h-100">
                                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-start">
                                            <div>
                                                {{ $cart_item->name }} <br>
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">{{ $cart_item->options->code }}</span>
                                                @include('livewire.includes.product-cart-modal')
                                            </div>
                                            <a href="#" wire:click.prevent="removeItem('{{ $cart_item->rowId }}')">
                                                <i class="bi bi-x-circle font-2xl text-danger"></i>
                                            </a>
                                        </div>
                                        <div class="flex-auto p-5">
                                            <ul class="list-group list-group-flush mb-0">
                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <span>{{ __('sale.price') }}</span>
                                                    <span>{{ format_currency($cart_item->price) }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <span>{{ __('sale.quantity') }}</span>
                                                    <span>@include('livewire.includes.product-cart-quantity')</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12 text-center">
                                <span class="text-danger">Please search &amp; select products!</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Order Tax ({{ $global_tax }}%)</span><span>(+) {{ format_currency(Cart::instance($cart_instance)->tax()) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Discount ({{ $global_discount }}%)</span><span>(-) {{ format_currency(Cart::instance($cart_instance)->discount()) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Shipping</span>
                                <span>
                                    <input type="hidden" value="{{ $shipping }}" name="shipping_amount">
                                    (+) {{ format_currency($shipping) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between text-primary">
                                <span class="fw-bold">Grand Total</span><span class="fw-bold">(=) {{ format_currency($total_with_shipping) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="col-lg-4">
                    <div class="mb-4">
                        <label for="tax_percentage">Order Tax (%)</label>
                        <input wire:model.blur="global_tax" type="number" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" min="0" max="100" value="{{ $global_tax }}" required>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mb-4">
                        <label for="discount_percentage">Discount (%)</label>
                        <input wire:model.blur="global_discount" type="number" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" min="0" max="100" value="{{ $global_discount }}" required>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mb-4">
                        <label for="shipping_amount">Shipping</label>
                        <input wire:model.blur="shipping" type="number" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" min="0" value="0" required step="0.01">
                    </div>
                </div>
            </div>

            <div class="mb-4 d-flex justify-content-center flex-wrap mb-0">
                <button wire:click="resetCart" type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default rounded-full bg-red-500 !text-white border-red-500 hover:bg-red-600 hover:border-red-600 mr-3"><i class="bi bi-x"></i> Reset</button>
                <button wire:loading.attr="disabled" wire:click="proceed" type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default rounded-full bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700" {{  $total_amount == 0 ? 'disabled' : '' }}><i class="bi bi-check"></i> Proceed</button>
            </div>
        </div>
    </div>

    {{--Checkout Modal--}}
    @include('livewire.pos.includes.checkout-modal')

</div>

