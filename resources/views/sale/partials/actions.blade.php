<div class="d-flex flex-wrap justify-content-center gap-1 inline-action-menu">
    <a target="_blank" href="{{ route('sales.pos.pdf', $data->id) }}" class="btn btn-sm btn-ghost-success rounded" title="POS Invoice">
        <i class="bi bi-file-earmark-pdf"></i>
    </a>
    @can('access_sale_payments')
        <a href="{{ route('sale-payments.index', $data->id) }}" class="btn btn-sm btn-ghost-warning rounded" title="Show Payments">
            <i class="bi bi-cash-coin"></i>
        </a>
    @endcan
    @can('access_sale_payments')
        @if($data->due_amount > 0)
        <a href="{{ route('sale-payments.create', $data->id) }}" class="btn btn-sm btn-ghost-success rounded" title="Add Payment">
            <i class="bi bi-plus-circle-dotted"></i>
        </a>
        @endif
    @endcan
    @can('edit_sales')
        <a href="{{ route('sales.edit', $data->id) }}" class="btn btn-sm btn-ghost-primary rounded" title="Edit">
            <i class="bi bi-pencil"></i>
        </a>
    @endcan
    @can('show_sales')
        <a href="{{ route('sales.show', $data->id) }}" class="btn btn-sm btn-ghost-info rounded" title="Details">
            <i class="bi bi-eye"></i>
        </a>
    @endcan
    @can('delete_sales')
        <button id="delete" class="btn btn-sm btn-ghost-danger rounded" title="Delete" data-submit-form="destroy{{ $data->id }}">
            <i class="bi bi-trash"></i>
            <form id="destroy{{ $data->id }}" class="d-none" action="{{ route('sales.destroy', $data->id) }}" method="POST">
                @csrf
                @method('delete')
            </form>
        </button>
    @endcan
</div>
