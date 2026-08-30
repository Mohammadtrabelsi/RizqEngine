<div class="btn-group dropdown inline-action-menu">
    <button type="button" class="btn btn-ghost-primary rounded" data-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
    </button>
    <div class="dropdown-menu">
        @can('show_commandes')
            <a href="{{ route('commandes.show', $data->id) }}" class="dropdown-item">
                <i class="bi bi-eye mr-2 text-info" style="line-height: 1;"></i> {{ __('commande.details') }}
            </a>
        @endcan
        @if($data->status === \App\Models\Commande::STATUS_PENDING)
            @can('confirm_commandes')
                <button class="dropdown-item" onclick="event.preventDefault(); document.getElementById('confirm-cmd-{{ $data->id }}').submit();">
                    <i class="bi bi-check2-circle mr-2 text-success" style="line-height: 1;"></i> {{ __('commande.confirm') }}
                    <form id="confirm-cmd-{{ $data->id }}" class="d-none" action="{{ route('commandes.confirm', $data->id) }}" method="POST">@csrf</form>
                </button>
            @endcan
        @endif
        @if(! $data->isInvoiced())
            @can('convert_commandes')
                <button class="dropdown-item" onclick="event.preventDefault(); document.getElementById('facture-cmd-{{ $data->id }}').submit();">
                    <i class="bi bi-receipt mr-2 text-success" style="line-height: 1;"></i> {{ __('commande.generate-facture') }}
                    <form id="facture-cmd-{{ $data->id }}" class="d-none" action="{{ route('commandes.convert', $data->id) }}" method="POST">@csrf</form>
                </button>
            @endcan
        @endif
        @can('delete_commandes')
            <button class="dropdown-item" onclick="event.preventDefault(); document.getElementById('destroy-cmd-{{ $data->id }}').submit();">
                <i class="bi bi-trash mr-2 text-danger" style="line-height: 1;"></i> {{ __('commande.delete') }}
                <form id="destroy-cmd-{{ $data->id }}" class="d-none" action="{{ route('commandes.destroy', $data->id) }}" method="POST">
                    @csrf
                    @method('delete')
                </form>
            </button>
        @endcan
    </div>
</div>
