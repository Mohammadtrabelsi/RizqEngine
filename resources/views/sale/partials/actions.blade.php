<div class="d-flex flex-wrap justify-content-center gap-1">
    <a target="_blank" href="{{ route('sales.pos.pdf', $data->id) }}" class="btn btn-sm btn-ghost-success rounded" title="POS Invoice">
        <i class="bi bi-file-earmark-pdf"></i>
    </a>
    <div class="btn-group dropdown inline-action-menu">
        <button type="button" class="btn btn-sm btn-ghost-primary rounded" data-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <div class="dropdown-menu">
            @can('access_sale_payments')
                <a href="{{ route('sale-payments.index', $data->id) }}" class="dropdown-item">
                    <i class="line-1 bi bi-cash-coin mr-2 text-warning"></i> Show Payments
                </a>
            @endcan
            @can('access_sale_payments')
                @if($data->due_amount > 0)
                    <a href="{{ route('sale-payments.create', $data->id) }}" class="dropdown-item">
                        <i class="line-1 bi bi-plus-circle-dotted mr-2 text-success"></i> Add Payment
                    </a>
                @endif
            @endcan
            @can('edit_sales')
                <a href="{{ route('sales.edit', $data->id) }}" class="dropdown-item">
                    <i class="line-1 bi bi-pencil mr-2 text-primary"></i> Edit
                </a>
            @endcan
            @can('show_sales')
                <a href="{{ route('sales.show', $data->id) }}" class="dropdown-item">
                    <i class="line-1 bi bi-eye mr-2 text-info"></i> Details
                </a>
            @endcan
            @can('delete_sales')
                <button id="delete" class="dropdown-item" data-submit-form="destroy{{ $data->id }}">
                    <i class="line-1 bi bi-trash mr-2 text-danger"></i> Delete
                    <form id="destroy{{ $data->id }}" class="d-none" action="{{ route('sales.destroy', $data->id) }}" method="POST">
                        @csrf
                        @method('delete')
                    </form>
                </button>
            @endcan
        </div>
    </div>
</div>
