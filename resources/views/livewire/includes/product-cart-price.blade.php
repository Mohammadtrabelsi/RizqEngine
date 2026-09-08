<div class="input-group d-flex justify-content-center">
    <input wire:model="unit_price.{{ $cart_item->id }}" type="text" class="form-control cart-item-input" min="0">
    <div class="input-group-append">
        <button @click="open{{ $cart_item->id }} = !open{{ $cart_item->id }}" type="button" wire:click="updatePrice('{{ $cart_item->rowId }}', {{ $cart_item->id }})" class="btn btn-info">
            <i class="bi bi-check"></i>
        </button>
    </div>
</div>
