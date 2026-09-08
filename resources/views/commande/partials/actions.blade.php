<div class="d-flex flex-wrap justify-content-center gap-2">
    @can('show_commandes')
        <a href="{{ route('commandes.show', $data->id) }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-eye me-1 text-info"></i> {{ __('commande.details') }}
        </a>
    @endcan
    @if($data->status === \App\Models\Commande::STATUS_PENDING)
        @can('confirm_commandes')
            <button type="button" class="btn btn-sm btn-secondary" data-submit-form="confirm-cmd-{{ $data->id }}">
                <i class="bi bi-check2-circle me-1 text-success"></i> {{ __('commande.confirm') }}
                <form id="confirm-cmd-{{ $data->id }}" class="d-none" action="{{ route('commandes.confirm', $data->id) }}" method="POST">@csrf</form>
            </button>
        @endcan
    @endif
    @if(! $data->hasBonLivraison())
        @can('convert_commandes_to_bon_livraison')
            <button type="button" class="btn btn-sm btn-secondary" data-submit-form="bl-cmd-{{ $data->id }}">
                <i class="bi bi-truck me-1 text-info"></i> {{ __('commande.create-bon-livraison') }}
                <form id="bl-cmd-{{ $data->id }}" class="d-none" action="{{ route('commandes.convert-bon-livraison', $data->id) }}" method="POST">@csrf</form>
            </button>
        @endcan
    @endif
    @if(! $data->hasStockExit())
        @can('convert_commandes_to_stock_exit')
            <button type="button" class="btn btn-sm btn-secondary" data-submit-form="bs-cmd-{{ $data->id }}">
                <i class="bi bi-box-arrow-up me-1 text-warning"></i> {{ __('commande.create-stock-exit') }}
                <form id="bs-cmd-{{ $data->id }}" class="d-none" action="{{ route('commandes.convert-stock-exit', $data->id) }}" method="POST">@csrf</form>
            </button>
        @endcan
    @endif
    @if(! $data->isInvoiced())
        @can('convert_commandes')
            <button type="button" class="btn btn-sm btn-secondary" data-submit-form="facture-cmd-{{ $data->id }}">
                <i class="bi bi-receipt me-1 text-success"></i> {{ __('commande.generate-facture') }}
                <form id="facture-cmd-{{ $data->id }}" class="d-none" action="{{ route('commandes.convert', $data->id) }}" method="POST">@csrf</form>
            </button>
        @endcan
    @endif
    @can('delete_commandes')
        <button type="button" class="btn btn-sm btn-outline-danger" data-submit-form="destroy-cmd-{{ $data->id }}">
            <i class="bi bi-trash me-1"></i> {{ __('commande.delete') }}
            <form id="destroy-cmd-{{ $data->id }}" class="d-none" action="{{ route('commandes.destroy', $data->id) }}" method="POST">
                @csrf
                @method('delete')
            </form>
        </button>
    @endcan
</div>
