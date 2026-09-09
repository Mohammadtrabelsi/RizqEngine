<div class="d-flex flex-wrap justify-content-center gap-2">
    @can('access_purchase_return_payments')
        <a href="{{ route('purchase-return-payments.index', $data->id) }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-cash-coin me-1 text-warning"></i> Show Payments
        </a>
    @endcan
    @can('access_purchase_return_payments')
        @if($data->due_amount > 0)
            <a href="{{ route('purchase-return-payments.create', $data->id) }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-plus-circle-dotted me-1 text-success"></i> Add Payment
            </a>
        @endif
    @endcan
    @can('edit_purchase_returns')
        <a href="{{ route('purchase-returns.edit', $data->id) }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-pencil me-1 text-primary"></i> Edit
        </a>
    @endcan
    @can('show_purchase_returns')
        <a href="{{ route('purchase-returns.show', $data->id) }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-eye me-1 text-info"></i> Details
        </a>
    @endcan
    @can('delete_purchase_return')
        <button type="button" class="btn btn-sm btn-outline-danger" data-submit-form="destroy{{ $data->id }}">
            <i class="bi bi-trash me-1"></i> Delete
            <form id="destroy{{ $data->id }}" class="d-none" action="{{ route('purchase-returns.destroy', $data->id) }}" method="POST">
                @csrf
                @method('delete')
            </form>
        </button>
    @endcan
</div>
