<div class="btn-group dropdown inline-action-menu">
    <button type="button" class="btn btn-ghost-primary rounded" data-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
    </button>
    <div class="dropdown-menu">
        @can('convert_quotations')
            @if(! $data->isConverted())
                <button class="dropdown-item" data-submit-form="convert-qt-{{ $data->id }}">
                    <i class="line-1 bi bi-arrow-right-circle mr-2 text-success"></i> {{ __('boncommande.transform-to-bon-commande') }}
                    <form id="convert-qt-{{ $data->id }}" class="d-none" action="{{ route('quotations.convert', $data->id) }}" method="POST">@csrf</form>
                </button>
            @else
                <a href="{{ route('bon-commandes.show', $data->bonCommande->id) }}" class="dropdown-item">
                    <i class="line-1 bi bi-box-arrow-up-right mr-2 text-info"></i> {{ __('boncommande.view-bon-commande') }}
                </a>
            @endif
        @endcan
        @can('create_quotation_sales')
            <a href="{{ route('quotation-sales.create', $data) }}" class="dropdown-item">
                <i class="line-1 bi bi-check2-circle mr-2 text-success"></i> Make Sale
            </a>
        @endcan
        @can('send_quotation_mails')
            <a href="{{ route('quotation.email', $data) }}" class="dropdown-item">
                <i class="line-1 bi bi-cursor mr-2 text-warning"></i> Send On Email
            </a>
        @endcan
        @can('edit_quotations')
            <a href="{{ route('quotations.edit', $data->id) }}" class="dropdown-item">
                <i class="line-1 bi bi-pencil mr-2 text-primary"></i> Edit
            </a>
        @endcan
        @can('show_quotations')
            <a href="{{ route('quotations.show', $data->id) }}" class="dropdown-item">
                <i class="line-1 bi bi-eye mr-2 text-info"></i> Details
            </a>
        @endcan
        @can('delete_quotations')
            <button id="delete" class="dropdown-item" data-submit-form="destroy{{ $data->id }}">
                <i class="line-1 bi bi-trash mr-2 text-danger"></i> Delete
                <form id="destroy{{ $data->id }}" class="d-none" action="{{ route('quotations.destroy', $data->id) }}" method="POST">
                    @csrf
                    @method('delete')
                </form>
            </button>
        @endcan
    </div>
</div>
