<span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline {{ $data->statusBadgeClass() }}">
    {{ __('commande.status_'.$data->status) }}
</span>
@if($data->shipping_status && $data->shipping_status !== \App\Models\Commande::SHIPPING_NOT_SHIPPED)
    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline {{ $data->shippingStatusBadgeClass() }}">
        {{ __('commande.shipping_'.$data->shipping_status) }}
    </span>
@endif
