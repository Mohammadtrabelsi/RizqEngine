<div class="btn-group dropdown inline-action-menu">
    <button type="button" class="btn btn-ghost-primary rounded" data-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
    </button>
    <div class="dropdown-menu">
        @can('show_bon_commandes')
            <a href="{{ route('bon-commandes.show', $data->id) }}" class="dropdown-item">
                <i class="bi bi-eye mr-2 text-info" style="line-height: 1;"></i> {{ __('boncommande.details') }}
            </a>
        @endcan
        @if($data->status === \App\Models\BonCommande::STATUS_DRAFT)
            @can('edit_bon_commandes')
                <a href="{{ route('bon-commandes.edit', $data->id) }}" class="dropdown-item">
                    <i class="bi bi-pencil mr-2 text-primary" style="line-height: 1;"></i> {{ __('boncommande.edit') }}
                </a>
            @endcan
            @can('confirm_bon_commandes')
                <button class="dropdown-item" onclick="event.preventDefault(); document.getElementById('confirm-bc-{{ $data->id }}').submit();">
                    <i class="bi bi-check2-circle mr-2 text-success" style="line-height: 1;"></i> {{ __('boncommande.confirm') }}
                    <form id="confirm-bc-{{ $data->id }}" class="d-none" action="{{ route('bon-commandes.confirm', $data->id) }}" method="POST">@csrf</form>
                </button>
            @endcan
        @endif
        @if($data->status === \App\Models\BonCommande::STATUS_CONFIRMED)
            @can('convert_bon_commandes')
                <button class="dropdown-item" onclick="event.preventDefault(); document.getElementById('convert-bc-{{ $data->id }}').submit();">
                    <i class="bi bi-arrow-right-circle mr-2 text-success" style="line-height: 1;"></i> {{ __('boncommande.convert-to-commande') }}
                    <form id="convert-bc-{{ $data->id }}" class="d-none" action="{{ route('bon-commandes.convert', $data->id) }}" method="POST">@csrf</form>
                </button>
            @endcan
        @endif
        @if(! $data->isConverted() && ! $data->isCancelled())
            @can('edit_bon_commandes')
                <button class="dropdown-item" onclick="event.preventDefault(); document.getElementById('cancel-bc-{{ $data->id }}').submit();">
                    <i class="bi bi-x-circle mr-2 text-warning" style="line-height: 1;"></i> {{ __('boncommande.cancel') }}
                    <form id="cancel-bc-{{ $data->id }}" class="d-none" action="{{ route('bon-commandes.cancel', $data->id) }}" method="POST">@csrf</form>
                </button>
            @endcan
        @endif
        @can('delete_bon_commandes')
            <button class="dropdown-item" onclick="event.preventDefault(); document.getElementById('destroy-bc-{{ $data->id }}').submit();">
                <i class="bi bi-trash mr-2 text-danger" style="line-height: 1;"></i> {{ __('boncommande.delete') }}
                <form id="destroy-bc-{{ $data->id }}" class="d-none" action="{{ route('bon-commandes.destroy', $data->id) }}" method="POST">
                    @csrf
                    @method('delete')
                </form>
            </button>
        @endcan
    </div>
</div>
