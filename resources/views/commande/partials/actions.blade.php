<div class="btn-group dropdown inline-action-menu">
    <button type="button" class="btn btn-ghost" data-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
    </button>
    <div class="dropdown-menu">
        @can('show_commandes')
            <a href="{{ route('commandes.show', $data->id) }}" class="dropdown-item">
                <i class="line-1 bi bi-eye mr-2 text-info"></i> {{ __('commande.details') }}
            </a>
        @endcan
        @if($data->status === \App\Models\Commande::STATUS_PENDING)
            @can('confirm_commandes')
                <button class="dropdown-item" data-submit-form="confirm-cmd-{{ $data->id }}">
                    <i class="line-1 bi bi-check2-circle mr-2 text-success"></i> {{ __('commande.confirm') }}
                    <form id="confirm-cmd-{{ $data->id }}" class="d-none" action="{{ route('commandes.confirm', $data->id) }}" method="POST">@csrf</form>
                </button>
            @endcan
        @endif
        @if(! $data->hasBonLivraison())
            @can('convert_commandes_to_bon_livraison')
                <button class="dropdown-item" data-submit-form="bl-cmd-{{ $data->id }}">
                    <i class="line-1 bi bi-truck mr-2 text-info"></i> {{ __('commande.create-bon-livraison') }}
                    <form id="bl-cmd-{{ $data->id }}" class="d-none" action="{{ route('commandes.convert-bon-livraison', $data->id) }}" method="POST">@csrf</form>
                </button>
            @endcan
        @endif
        @if(! $data->hasStockExit())
            @can('convert_commandes_to_stock_exit')
                <button class="dropdown-item" data-submit-form="bs-cmd-{{ $data->id }}">
                    <i class="line-1 bi bi-box-arrow-up mr-2 text-warning"></i> {{ __('commande.create-stock-exit') }}
                    <form id="bs-cmd-{{ $data->id }}" class="d-none" action="{{ route('commandes.convert-stock-exit', $data->id) }}" method="POST">@csrf</form>
                </button>
            @endcan
        @endif
        @if(! $data->isInvoiced())
            @can('convert_commandes')
                <button class="dropdown-item" data-submit-form="facture-cmd-{{ $data->id }}">
                    <i class="line-1 bi bi-receipt mr-2 text-success"></i> {{ __('commande.generate-facture') }}
                    <form id="facture-cmd-{{ $data->id }}" class="d-none" action="{{ route('commandes.convert', $data->id) }}" method="POST">@csrf</form>
                </button>
            @endcan
        @endif
        @can('delete_commandes')
            <button class="dropdown-item" data-submit-form="destroy-cmd-{{ $data->id }}">
                <i class="line-1 bi bi-trash mr-2 text-danger"></i> {{ __('commande.delete') }}
                <form id="destroy-cmd-{{ $data->id }}" class="d-none" action="{{ route('commandes.destroy', $data->id) }}" method="POST">
                    @csrf
                    @method('delete')
                </form>
            </button>
        @endcan
    </div>
</div>
