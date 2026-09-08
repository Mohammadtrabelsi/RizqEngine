<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $bonLivraisons->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($bonLivraisons as $bonLivraison)
            <div class="col-xl-4 col-lg-6 mb-4" wire:key="bon-livraison-{{ $bonLivraison->id }}">
                <div class="card h-100">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="fw-bold me-2">{{ $bonLivraison->reference }}</span>
                            @include('bonlivraison.partials.status', ['data' => $bonLivraison])
                        </div>
                        @include('bonlivraison.partials.actions', ['data' => $bonLivraison])
                    </div>
                    <div class="flex-auto p-2">
                        <h6 class="mb-3"><i class="bi bi-person"></i> {{ $bonLivraison->customer_name }}</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('bonlivraison.date') }}</span><span>{{ $bonLivraison->date }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('bonlivraison.total') }}</span><span>{{ format_currency($bonLivraison->total_amount) }}</span></li>
                            @if($bonLivraison->commande_id)
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('commande.commande') }}</span><a href="{{ route('commandes.show', $bonLivraison->commande_id) }}">{{ $bonLivraison->commande->reference ?? '' }}</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('bonlivraison.none-found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $bonLivraisons->links('pagination::bootstrap-5') }}
    </div>
</div>
