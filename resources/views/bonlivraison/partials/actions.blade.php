<div class="d-flex flex-wrap justify-content-center gap-2">
    @can('show_bon_livraisons')
        <a href="{{ route('bon-livraisons.show', $data->id) }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-eye me-1 text-info"></i> {{ __('bonlivraison.details') }}
        </a>
    @endcan
    @if($data->status === \App\Models\BonLivraison::STATUS_PENDING)
        @can('deliver_bon_livraisons')
            <button type="button" class="btn btn-sm btn-secondary" data-submit-form="deliver-bl-{{ $data->id }}">
                <i class="bi bi-truck me-1 text-primary"></i> {{ __('bonlivraison.mark-delivered') }}
                <form id="deliver-bl-{{ $data->id }}" class="d-none" action="{{ route('bon-livraisons.deliver', $data->id) }}" method="POST">@csrf</form>
            </button>
        @endcan
    @endif
    @if(! $data->isInvoiced())
        @can('convert_bon_livraisons')
            <button type="button" class="btn btn-sm btn-secondary" data-submit-form="facture-bl-{{ $data->id }}">
                <i class="bi bi-receipt me-1 text-success"></i> {{ __('bonlivraison.generate-facture') }}
                <form id="facture-bl-{{ $data->id }}" class="d-none" action="{{ route('bon-livraisons.convert', $data->id) }}" method="POST">@csrf</form>
            </button>
        @endcan
    @endif
    @can('delete_bon_livraisons')
        <button type="button" class="btn btn-sm btn-outline-danger" data-submit-form="destroy-bl-{{ $data->id }}">
            <i class="bi bi-trash me-1"></i> {{ __('bonlivraison.delete') }}
            <form id="destroy-bl-{{ $data->id }}" class="d-none" action="{{ route('bon-livraisons.destroy', $data->id) }}" method="POST">
                @csrf
                @method('delete')
            </form>
        </button>
    @endcan
</div>
