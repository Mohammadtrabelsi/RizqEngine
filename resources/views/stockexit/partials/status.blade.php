@if($data->status === \App\Models\StockExit::STATUS_CLOSED)
    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-success">{{ __('stockexit.status_closed') }}</span>
@else
    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-warning text-dark">{{ __('stockexit.status_in_transit') }}</span>
@endif
