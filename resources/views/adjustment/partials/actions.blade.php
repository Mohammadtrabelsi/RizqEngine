@can('edit_adjustments')
    <a href="{{ route('adjustments.edit', $data->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-indigo-600 border-indigo-600 hover:bg-indigo-600 hover:!text-white !px-3 !py-1.5 !text-xs">
        <i class="bi bi-pencil"></i>
    </a>
@endcan
@can('show_adjustments')
    <a href="{{ route('adjustments.show', $data->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-indigo-600 border-indigo-600 hover:bg-indigo-600 hover:!text-white !px-3 !py-1.5 !text-xs">
        <i class="bi bi-eye"></i>
    </a>
@endcan
@can('delete_adjustments')
    <button id="delete" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-red-500 border-red-500 hover:bg-red-500 hover:!text-white !px-3 !py-1.5 !text-xs" data-submit-form="destroy{{ $data->id }}">
        <i class="bi bi-trash"></i>
        <form id="destroy{{ $data->id }}" class="d-none" action="{{ route('adjustments.destroy', $data->id) }}" method="POST">
            @csrf
            @method('delete')
        </form>
    </button>
@endcan
