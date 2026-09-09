<div class="d-flex flex-wrap justify-content-center gap-2">
    @can('convert_quotations')
        @if(! $data->isConverted())
            <button type="button" class="btn btn-sm btn-secondary" data-submit-form="convert-qt-{{ $data->id }}">
                <i class="bi bi-arrow-right-circle me-1 text-success"></i> {{ __('boncommande.transform-to-bon-commande') }}
                <form id="convert-qt-{{ $data->id }}" class="d-none" action="{{ route('quotations.convert', $data->id) }}" method="POST">@csrf</form>
            </button>
        @elseif($data->bonCommande)
            <a href="{{ route('bon-commandes.show', $data->bonCommande->id) }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-box-arrow-up-right me-1 text-info"></i> {{ __('boncommande.view-bon-commande') }}
            </a>
        @endif
    @endcan
    @can('convert_quotations_to_commande')
        @if(! $data->isConverted())
            <button type="button" class="btn btn-sm btn-secondary" data-submit-form="convert-qt-cmd-{{ $data->id }}">
                <i class="bi bi-arrow-right-circle me-1 text-success"></i> {{ __('commande.transform-to-commande') }}
                <form id="convert-qt-cmd-{{ $data->id }}" class="d-none" action="{{ route('quotations.convert-commande', $data->id) }}" method="POST">@csrf</form>
            </button>
        @elseif($data->commande)
            <a href="{{ route('commandes.show', $data->commande->id) }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-box-arrow-up-right me-1 text-info"></i> {{ __('commande.view-commande') }}
            </a>
        @endif
    @endcan
    @can('create_quotation_sales')
        <a href="{{ route('quotation-sales.create', $data) }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-check2-circle me-1 text-success"></i> {{ __('quotations.make_sale') }}
        </a>
    @endcan
    @can('send_quotation_mails')
        <a href="{{ route('quotation.email', $data) }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-cursor me-1 text-warning"></i> {{ __('quotations.send_on_email') }}
        </a>
    @endcan
    @can('edit_quotations')
        <a href="{{ route('quotations.edit', $data->id) }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-pencil me-1 text-primary"></i> {{ __('quotations.edit') }}
        </a>
    @endcan
    @can('show_quotations')
        <a href="{{ route('quotations.show', $data->id) }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-eye me-1 text-info"></i> {{ __('quotations.details') }}
        </a>
    @endcan
    @can('delete_quotations')
        <button type="button" class="btn btn-sm btn-outline-danger" data-submit-form="destroy{{ $data->id }}">
            <i class="bi bi-trash me-1"></i> {{ __('quotations.delete') }}
            <form id="destroy{{ $data->id }}" class="d-none" action="{{ route('quotations.destroy', $data->id) }}" method="POST">
                @csrf
                @method('delete')
            </form>
        </button>
    @endcan
</div>
