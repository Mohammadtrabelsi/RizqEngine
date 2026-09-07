<div class="btn-group dropdown inline-action-menu">
    <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-indigo-600 bg-transparent border-transparent px-2 hover:bg-indigo-50 rounded" data-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
    </button>
    <div class="dropdown-menu">
        @can('convert_quotations')
            @if(! $data->isConverted())
                <button class="dropdown-item" data-submit-form="convert-qt-{{ $data->id }}">
                    <i class="line-1 bi bi-arrow-right-circle mr-2 text-success"></i> {{ __('boncommande.transform-to-bon-commande') }}
                    <form id="convert-qt-{{ $data->id }}" class="d-none" action="{{ route('quotations.convert', $data->id) }}" method="POST">@csrf</form>
                </button>
            @elseif($data->bonCommande)
                <a href="{{ route('bon-commandes.show', $data->bonCommande->id) }}" class="dropdown-item">
                    <i class="line-1 bi bi-box-arrow-up-right mr-2 text-info"></i> {{ __('boncommande.view-bon-commande') }}
                </a>
            @endif
        @endcan
        @can('convert_quotations_to_commande')
            @if(! $data->isConverted())
                <button class="dropdown-item" data-submit-form="convert-qt-cmd-{{ $data->id }}">
                    <i class="line-1 bi bi-arrow-right-circle mr-2 text-success"></i> {{ __('commande.transform-to-commande') }}
                    <form id="convert-qt-cmd-{{ $data->id }}" class="d-none" action="{{ route('quotations.convert-commande', $data->id) }}" method="POST">@csrf</form>
                </button>
            @elseif($data->commande)
                <a href="{{ route('commandes.show', $data->commande->id) }}" class="dropdown-item">
                    <i class="line-1 bi bi-box-arrow-up-right mr-2 text-info"></i> {{ __('commande.view-commande') }}
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
