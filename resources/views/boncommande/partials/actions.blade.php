<div class="d-flex flex-wrap justify-content-center gap-2">
    @can('show_bon_commandes')
        <a href="{{ route('bon-commandes.show', $data->id) }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-eye me-1 text-info"></i> {{ __('boncommande.details') }}
        </a>
    @endcan
    @if($data->status === \App\Models\BonCommande::STATUS_DRAFT)
        @can('edit_bon_commandes')
            <a href="{{ route('bon-commandes.edit', $data->id) }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-pencil me-1 text-primary"></i> {{ __('boncommande.edit') }}
            </a>
        @endcan
        @can('confirm_bon_commandes')
            <button type="button" class="btn btn-sm btn-secondary" data-submit-form="confirm-bc-{{ $data->id }}">
                <i class="bi bi-check2-circle me-1 text-success"></i> {{ __('boncommande.confirm') }}
                <form id="confirm-bc-{{ $data->id }}" class="d-none" action="{{ route('bon-commandes.confirm', $data->id) }}" method="POST">@csrf</form>
            </button>
        @endcan
    @endif
    @if($data->status === \App\Models\BonCommande::STATUS_CONFIRMED)
        @can('convert_bon_commandes')
            <button type="button" class="btn btn-sm btn-secondary" data-submit-form="convert-bc-{{ $data->id }}">
                <i class="bi bi-arrow-right-circle me-1 text-success"></i> {{ __('boncommande.convert-to-commande') }}
                <form id="convert-bc-{{ $data->id }}" class="d-none" action="{{ route('bon-commandes.convert', $data->id) }}" method="POST">@csrf</form>
            </button>
        @endcan
    @endif
    @if(! $data->isConverted() && ! $data->isCancelled())
        @can('edit_bon_commandes')
            <button type="button" class="btn btn-sm btn-secondary" data-submit-form="cancel-bc-{{ $data->id }}">
                <i class="bi bi-x-circle me-1 text-warning"></i> {{ __('boncommande.cancel') }}
                <form id="cancel-bc-{{ $data->id }}" class="d-none" action="{{ route('bon-commandes.cancel', $data->id) }}" method="POST">@csrf</form>
            </button>
        @endcan
    @endif
    @can('delete_bon_commandes')
        <button type="button" class="btn btn-sm btn-outline-danger" data-submit-form="destroy-bc-{{ $data->id }}">
            <i class="bi bi-trash me-1"></i> {{ __('boncommande.delete') }}
            <form id="destroy-bc-{{ $data->id }}" class="d-none" action="{{ route('bon-commandes.destroy', $data->id) }}" method="POST">
                @csrf
                @method('delete')
            </form>
        </button>
    @endcan
</div>
