<a href="{{ route('expense-categories.edit', $data->id) }}" class="btn btn-info btn-sm">
    <i class="bi bi-pencil"></i>
</a>
<button id="delete" class="btn btn-danger btn-sm" data-confirm-delete="destroy{{ $data->id }}">
    <i class="bi bi-trash"></i>
    <form id="destroy{{ $data->id }}" class="d-none" action="{{ route('expense-categories.destroy', $data->id) }}" method="POST">
        @csrf
        @method('delete')
    </form>
</button>
