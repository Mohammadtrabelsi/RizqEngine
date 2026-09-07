<div class="btn-group dropdown inline-action-menu">
    <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-indigo-600 bg-transparent border-transparent px-2 hover:bg-indigo-50 rounded" data-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
    </button>
    <div class="dropdown-menu">
        @can('access_sale_payments')
            <a href="{{ route('sale-return-payments.index', $data->id) }}" class="dropdown-item">
                <i class="line-1 bi bi-cash-coin mr-2 text-warning"></i> Show Payments
            </a>
        @endcan
        @can('access_sale_payments')
            @if($data->due_amount > 0)
                <a href="{{ route('sale-return-payments.create', $data->id) }}" class="dropdown-item">
                    <i class="line-1 bi bi-plus-circle-dotted mr-2 text-success"></i> Add Payment
                </a>
            @endif
        @endcan
        @can('edit_sales')
            <a href="{{ route('sale-returns.edit', $data->id) }}" class="dropdown-item">
                <i class="line-1 bi bi-pencil mr-2 text-primary"></i> Edit
            </a>
        @endcan
        @can('show_sales')
            <a href="{{ route('sale-returns.show', $data->id) }}" class="dropdown-item">
                <i class="line-1 bi bi-eye mr-2 text-info"></i> Details
            </a>
        @endcan
        @can('delete_sales')
            <button id="delete" class="dropdown-item" data-submit-form="destroy{{ $data->id }}">
                <i class="line-1 bi bi-trash mr-2 text-danger"></i> Delete
                <form id="destroy{{ $data->id }}" class="d-none" action="{{ route('sale-returns.destroy', $data->id) }}" method="POST">
                    @csrf
                    @method('delete')
                </form>
            </button>
        @endcan
    </div>
</div>
