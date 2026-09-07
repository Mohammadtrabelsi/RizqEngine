@if ($data->status == 'Pending')
    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-cyan-100 text-cyan-700">
        {{ $data->status }}
    </span>
@elseif ($data->status == 'Ordered')
    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-indigo-100 text-indigo-700">
        {{ $data->status }}
    </span>
@else
    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">
        {{ $data->status }}
    </span>
@endif
