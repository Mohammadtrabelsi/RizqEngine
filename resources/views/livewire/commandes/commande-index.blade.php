<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $commandes->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($commandes as $commande)
            <div class="col-xl-4 col-lg-6 mb-4" wire:key="commande-{{ $commande->id }}">
                <div class="card h-100">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
                        <span class="fw-bold me-2">{{ $commande->reference }}</span>
                        @include('commande.partials.status', ['data' => $commande])
                    </div>
                    <div class="flex-auto p-2">
                        <h6 class="mb-3"><i class="bi bi-person"></i> {{ $commande->customer_name }}</h6>
                        <ul class="list-group list-group-flush mb-0">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('commande.date') }}</span><span>{{ $commande->date }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('commande.total') }}</span><span>{{ format_currency($commande->total_amount) }}</span></li>
                            @if($commande->bon_commande_id)
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('boncommande.bon_commande') }}</span><a href="{{ route('bon-commandes.show', $commande->bon_commande_id) }}">{{ $commande->bonCommande->reference ?? '' }}</a></li>
                            @endif
                        </ul>
                    </div>
                    <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
                        @include('commande.partials.actions', ['data' => $commande])
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('commande.none-found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $commandes->links('pagination::bootstrap-5') }}
    </div>
</div>
