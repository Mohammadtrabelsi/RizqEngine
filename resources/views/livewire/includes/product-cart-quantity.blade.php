<div class="input-group d-flex justify-content-center">
    <input wire:key="qty-input-{{ $cart_item->rowId }}" wire:model="quantity.{{ $cart_item->id }}" type="number" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 cart-item-input" value="{{ $cart_item->qty }}" min="1">
    <div class="input-group-append">
        <button type="button" wire:click="updateQuantity('{{ $cart_item->rowId }}', {{ $cart_item->id }})" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-cyan-500 !text-white border-cyan-500 hover:bg-cyan-600">
            <i class="bi bi-check"></i>
        </button>
    </div>
</div>
