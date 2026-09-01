@can('edit_adjustments')
    <a href="{{ route('adjustments.edit', $data->id) }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-pencil"></i>
    </a>
@endcan
@can('show_adjustments')
    <a href="{{ route('adjustments.show', $data->id) }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-eye"></i>
    </a>
@endcan
@can('delete_adjustments')
    <button id="delete" class="btn btn-outline-danger btn-sm" data-submit-form="destroy{{ $data->id }}">
        <i class="bi bi-trash"></i>
        <form id="destroy{{ $data->id }}" class="d-none" action="{{ route('adjustments.destroy', $data->id) }}" method="POST">
            @csrf
            @method('delete')
        </form>
    </button>
@endcan
