<div>
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
        <div class="position-relative">
            <div wire:loading.flex class="col-12 position-absolute justify-content-center align-items-center wire-loading-overlay">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">{{ __('general.loading') }}...</span>
                </div>
            </div>
            <div class="card mb-4">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                    <h5 class="mb-0">{{ __('general.products') }}</h5>
                </div>
                <div class="flex-auto p-2">
                    <div class="row">
                        @if($cart_items->isNotEmpty())
                            @foreach($cart_items as $cart_item)
                                <div class="col-xl-4 col-lg-6 mb-4">
                                    <div class="card h-100">
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
                                <div class="flex-auto p-2">
                                    <ul class="list-group list-group-flush mb-0">
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>{{ __('general.net-unit-price') }}</span>
                                            <span>
                                                <span class="js-price-summary" data-toggle="price-detail" role="button" tabindex="0">{{ format_currency($cart_item->price) }}</span>
                                                <div class="js-price-detail" hidden>
                                                    @include('livewire.includes.product-cart-price')
                                                </div>
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>{{ __('general.stock') }}</span>
                                            <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-cyan-100 text-cyan-700">{{ $cart_item->options->stock . ' ' . $cart_item->options->unit }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>{{ __('general.quantity') }}</span>
                                            <span>@include('livewire.includes.product-cart-quantity')</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>{{ __('general.discount') }}</span>
                                            <span>{{ format_currency($cart_item->options->product_discount) }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>{{ __('general.tax') }}</span>
                                            <span>{{ format_currency($cart_item->options->product_tax) }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>{{ __('general.sub-total') }}</span>
                                            <span class="fw-bold">{{ format_currency($cart_item->options->sub_total) }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center">
                        <span class="text-danger">{{ __('general.please-search-and-select-products') }}</span>
                    </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-md-end">
        <div class="col-md-4">
            <div class="card">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="fw-bold">Tax ({{ $global_tax }}%)</span><span>(+) {{ format_currency(Cart::instance($cart_instance)->tax()) }}</span>
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
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="fw-bold">{{ __('withholding.total_ttc') }}</span><span class="fw-bold">(=) {{ format_currency($total_with_shipping) }}</span>
                    </li>
                    @if(in_array($cart_instance, ['purchase', 'sale'], true) && $withholding['total'] > 0)
                        @foreach($withholding['lines'] as $line)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ $line['name'] }} ({{ rtrim(rtrim(number_format($line['rate'], 3), '0'), '.') }}%)</span>
                                <span>(-) {{ format_currency($line['amount']) }}</span>
                            </li>
                        @endforeach
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">{{ __('withholding.withholding') }}</span><span>(-) {{ format_currency($withholding['total']) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">{{ __('withholding.net_payable') }}</span><span class="fw-bold">(=) {{ format_currency($withholding['net_payable']) }}</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <input type="hidden" name="total_amount" value="{{ $total_with_shipping }}">
    @if(in_array($cart_instance, ['purchase', 'sale'], true))
        @foreach($selected_withholding_taxes as $wht_id)
            <input type="hidden" name="withholding_tax_ids[]" value="{{ $wht_id }}">
        @endforeach
    @endif

    <div class="form-row">
        <div class="col-lg-4">
            @if(in_array($cart_instance, ['purchase', 'quotation'], true))
                <div class="mb-4">
                    <label for="tax_mode">{{ __('taxes.tax_mode') }}</label>
                    <select wire:model.live="tax_mode" id="tax_mode" class="form-control">
                        <option value="included">{{ __('taxes.tax_included') }}</option>
                        <option value="excluded">{{ __('taxes.tax_excluded') }}</option>
                    </select>
                    <input type="hidden" name="tax_percentage" value="{{ (float) $global_tax }}">
                </div>

                @if($tax_mode === 'excluded')
                    <div class="mb-4">
                        <label>{{ __('taxes.select_taxes') }}</label>
                        @forelse($available_taxes as $tax)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model.live="selected_taxes" value="{{ $tax->id }}" id="tax-{{ $tax->id }}">
                                <label class="form-check-label" for="tax-{{ $tax->id }}">
                                    {{ $tax->name }} ({{ $tax->type === 'fixed' ? format_currency($tax->rate) : rtrim(rtrim(number_format($tax->rate, 2), '0'), '.').'%' }})
                                </label>
                            </div>
                        @empty
                            <p class="text-muted mb-0">
                                {{ __('taxes.no_taxes_defined') }}
                                <a href="{{ route('taxes.create') }}" target="_blank">{{ __('taxes.add_tax') }}</a>
                            </p>
                        @endforelse
                    </div>
                @endif
            @else
                <div class="mb-4">
                    <label for="tax_percentage">{{ __('general.tax') }} (%)</label>
                    <input wire:model.blur="global_tax" type="number" class="form-control" name="tax_percentage" min="0" max="100" value="{{ $global_tax }}" required>
                </div>
            @endif
        </div>
        <div class="col-lg-4">
            <div class="mb-4">
                <label for="discount_percentage">{{ __('general.discount') }} (%)</label>
                <input wire:model.blur="global_discount" type="number" class="form-control" name="discount_percentage" min="0" max="100" value="{{ $global_discount }}" required>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="mb-4">
                <label for="shipping_amount">{{ __('general.shipping') }}</label>
                <input wire:model.blur="shipping" type="number" class="form-control" name="shipping_amount" min="0" value="0" required step="0.01">
            </div>
        </div>
    </div>

    {{-- Fiscalité : retenue à la source (RAS). Deducted after the TTC to give the net payable. --}}
    @if(in_array($cart_instance, ['purchase', 'sale'], true))
    <div class="form-row">
        <div class="col-lg-12">
            <div class="mb-4">
                <label class="fw-bold">{{ __('withholding.withholding_taxes') }}</label>
                @forelse($available_withholding_taxes as $withholding_tax)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model.live="selected_withholding_taxes" value="{{ $withholding_tax->id }}" id="wht-{{ $withholding_tax->id }}">
                        <label class="form-check-label" for="wht-{{ $withholding_tax->id }}">
                            {{ $withholding_tax->name }}
                            ({{ rtrim(rtrim(number_format($withholding_tax->rate, 3), '0'), '.') }}% · {{ __($withholding_tax->calculation_base->label()) }})
                        </label>
                    </div>
                @empty
                    <p class="text-muted mb-0">
                        {{ __('withholding.no_withholding_defined') }}
                        @can('create_withholding_taxes')
                            <a href="{{ route('withholding-taxes.create') }}" target="_blank">{{ __('withholding.add_withholding') }}</a>
                        @endcan
                    </p>
                @endforelse
            </div>
        </div>
    </div>
    @endif
</div>
