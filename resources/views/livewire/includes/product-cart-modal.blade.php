<div class="d-inline-block">
    <!-- Button trigger Discount Modal -->
    <span wire:click="$dispatch('discountModalRefresh', { product_id: {{ $cart_item->id }}, row_id: '{{ $cart_item->rowId }}' })" role="button" class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-amber-100 text-amber-700 pointer-event" data-toggle="modal" data-target="#discountModal{{ $cart_item->id }}">
        <i class="bi bi-pencil-square text-white"></i>
    </span>
    <!-- Discount Modal -->
    <div wire:ignore.self class="modal fade" id="discountModal{{ $cart_item->id }}" tabindex="-1" role="dialog" aria-labelledby="discountModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="discountModalLabel">
                        {{ $cart_item->name }}
                        <br>
                        <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">
                        {{ $cart_item->options->code }}
                    </span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if (session()->has('discount_message' . $cart_item->id))
                        <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-emerald-50 text-emerald-700 border-emerald-200 pr-12 fade show" role="alert">
                            <div class="alert-body">
                                <span>{{ session('discount_message' . $cart_item->id) }}</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                        </div>
                    @endif
                    <div class="mb-4">
                        <label>Discount Type <span class="text-danger">*</span></label>
                        <select wire:model.live="discount_type.{{ $cart_item->id }}" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" required>
                            <option value="fixed">{{ __('product.fixed') }}</option>
                            <option value="percentage">{{ __('product.percentage') }}</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        @if($discount_type[$cart_item->id] == 'percentage')
                            <label>Discount(%) <span class="text-danger">*</span></label>
                            <input wire:model="item_discount.{{ $cart_item->id }}" type="number" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" value="{{ $item_discount[$cart_item->id] }}" min="0" max="100">
                        @elseif($discount_type[$cart_item->id] == 'fixed')
                            <label>Discount <span class="text-danger">*</span></label>
                            <input wire:model="item_discount.{{ $cart_item->id }}" type="number" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" value="{{ $item_discount[$cart_item->id] }}">
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-white !text-slate-700 border-slate-300 hover:bg-slate-50 hover:!text-slate-900 hover:border-slate-400" data-dismiss="modal">{{ __('product.close') }}</button>
                    <button wire:click="setProductDiscount('{{ $cart_item->rowId }}', {{ $cart_item->id }})" type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">{{ __('product.save-changes') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
