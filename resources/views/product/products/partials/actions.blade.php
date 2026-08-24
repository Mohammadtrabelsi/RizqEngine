@can('edit_products')
<a href="{{ route('products.edit', $data->id) }}" class="btn btn-outline-info btn-sm">
    <i class="bi bi-pencil"></i>
</a>
@endcan
@can('show_products')
<a href="{{ route('products.show', $data->id) }}" class="btn btn-outline-primary btn-sm">
    <i class="bi bi-eye"></i>
</a>
@endcan
@can('delete_products')
<button id="delete" class="btn btn-outline-danger btn-sm" onclick="
    event.preventDefault();
    {
        document.getElementById('destroy{{ $data->id }}').submit()
    }
    ">
    <i class="bi bi-trash"></i>
    <form id="destroy{{ $data->id }}" class="d-none" action="{{ route('products.destroy', $data->id) }}" method="POST">
        @csrf
        @method('delete')
    </form>
</button>
@endcan
