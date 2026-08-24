<div>
    <div>
        @if (session()->has('message'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
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
                <div class="card-header">
                    <h5 class="mb-0">{{ __('general.products') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($cart_items->isNotEmpty())
                            @foreach($cart_items as $cart_item)
                                <div class="col-xl-4 col-lg-6 mb-4">
                                    <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-start">
                                    <div>
                                        {{ $cart_item->name }} <br>
                                        <span class="badge badge-success">{{ $cart_item->options->code }}</span>
                                        @include('livewire.includes.product-cart-modal')
                                    </div>
                                    <a href="#" wire:click.prevent="removeItem('{{ $cart_item->rowId }}')">
                                        <i class="bi bi-x-circle font-2xl text-danger"></i>
                                    </a>
                                </div>
                                <div class="card-body">
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
                                            <span class="badge badge-info">{{ $cart_item->options->stock . ' ' . $cart_item->options->unit }}</span>
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
                        <span class="fw-bold">Grand Total</span><span class="fw-bold">(=) {{ format_currency($total_with_shipping) }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <input type="hidden" name="total_amount" value="{{ $total_with_shipping }}">

    <div class="form-row">
        <div class="col-lg-4">
            <div class="form-group">
                <label for="tax_percentage">{{ __('general.tax') }} (%)</label>
                <input wire:model.blur="global_tax" type="number" class="form-control" name="tax_percentage" min="0" max="100" value="{{ $global_tax }}" required>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label for="discount_percentage">{{ __('general.discount') }} (%)</label>
                <input wire:model.blur="global_discount" type="number" class="form-control" name="discount_percentage" min="0" max="100" value="{{ $global_discount }}" required>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label for="shipping_amount">{{ __('general.shipping') }}</label>
                <input wire:model.blur="shipping" type="number" class="form-control" name="shipping_amount" min="0" value="0" required step="0.01">
            </div>
        </div>
    </div>
</div>
