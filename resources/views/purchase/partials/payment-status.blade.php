@if ($data->payment_status == 'Partial')
    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-amber-100 text-amber-700">
        {{ $data->payment_status }}
    </span>
@elseif ($data->payment_status == 'Paid')
    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">
        {{ $data->payment_status }}
    </span>
@else
    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-red-100 text-red-700">
        {{ $data->payment_status }}
    </span>
@endif
