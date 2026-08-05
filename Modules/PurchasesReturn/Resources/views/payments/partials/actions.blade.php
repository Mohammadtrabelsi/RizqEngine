@can('access_purchase_return_payments')
    <a href="{{ route('purchase-return-payments.edit', [$data->purchaseReturn->id, $data->id]) }}" class="btn btn-info btn-sm">
        <i class="bi bi-pencil"></i>
    </a>
@endcan
@can('access_purchase_return_payments')
    <button id="delete" class="btn btn-danger btn-sm" data-confirm-delete="destroy{{ $data->id }}">
        <i class="bi bi-trash"></i>
        <form id="destroy{{ $data->id }}" class="d-none" action="{{ route('purchase-return-payments.destroy', $data->id) }}" method="POST">
            @csrf
            @method('delete')
        </form>
    </button>
@endcan
