<div class="btn-group dropdown inline-action-menu">
    <button type="button" class="btn btn-ghost" data-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
    </button>
    <div class="dropdown-menu">
        @can('show_bon_livraisons')
            <a href="{{ route('bon-livraisons.show', $data->id) }}" class="dropdown-item">
                <i class="line-1 bi bi-eye mr-2 text-info"></i> {{ __('bonlivraison.details') }}
            </a>
        @endcan
        @if($data->status === \App\Models\BonLivraison::STATUS_PENDING)
            @can('deliver_bon_livraisons')
                <button class="dropdown-item" data-submit-form="deliver-bl-{{ $data->id }}">
                    <i class="line-1 bi bi-truck mr-2 text-primary"></i> {{ __('bonlivraison.mark-delivered') }}
                    <form id="deliver-bl-{{ $data->id }}" class="d-none" action="{{ route('bon-livraisons.deliver', $data->id) }}" method="POST">@csrf</form>
                </button>
            @endcan
        @endif
        @if(! $data->isInvoiced())
            @can('convert_bon_livraisons')
                <button class="dropdown-item" data-submit-form="facture-bl-{{ $data->id }}">
                    <i class="line-1 bi bi-receipt mr-2 text-success"></i> {{ __('bonlivraison.generate-facture') }}
                    <form id="facture-bl-{{ $data->id }}" class="d-none" action="{{ route('bon-livraisons.convert', $data->id) }}" method="POST">@csrf</form>
                </button>
            @endcan
        @endif
        @can('delete_bon_livraisons')
            <button class="dropdown-item" data-submit-form="destroy-bl-{{ $data->id }}">
                <i class="line-1 bi bi-trash mr-2 text-danger"></i> {{ __('bonlivraison.delete') }}
                <form id="destroy-bl-{{ $data->id }}" class="d-none" action="{{ route('bon-livraisons.destroy', $data->id) }}" method="POST">
                    @csrf
                    @method('delete')
                </form>
            </button>
        @endcan
    </div>
</div>
