<div>
    {{-- Filters container --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
            <h5 class="mb-0"><i class="bi bi-funnel text-primary"></i> {{ __('app.filters') }}</h5>
        </div>
        <div class="flex-auto p-2">
            <div class="row align-items-end">
                <div class="col-12 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('app.search') }}</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('boncommande.status_label') }}</label>
                    <select wire:model.live="status" class="form-select">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="{{ \App\Models\BonCommande::STATUS_DRAFT }}">{{ __('boncommande.status_draft') }}</option>
                        <option value="{{ \App\Models\BonCommande::STATUS_CONFIRMED }}">{{ __('boncommande.status_confirmed') }}</option>
                        <option value="{{ \App\Models\BonCommande::STATUS_CONVERTED }}">{{ __('boncommande.status_converted') }}</option>
                        <option value="{{ \App\Models\BonCommande::STATUS_CANCELLED }}">{{ __('boncommande.status_cancelled') }}</option>
                    </select>
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <button type="button" wire:click="resetFilters" class="btn btn-secondary w-100">
                        <i class="bi bi-x-circle"></i> {{ __('app.reset') }}
                    </button>
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('reports.start_date') }}</label>
                    <input type="date" wire:model.live="dateFrom" class="form-control">
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('reports.end_date') }}</label>
                    <input type="date" wire:model.live="dateTo" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $bonCommandes->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($bonCommandes as $bonCommande)
            <div class="col-xl-4 col-lg-6 mb-4" wire:key="bon-commande-{{ $bonCommande->id }}">
                <div class="card h-100">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
                        <span class="fw-bold me-2">{{ $bonCommande->reference }}</span>
                        @include('boncommande.partials.status', ['data' => $bonCommande])
                    </div>
                    <div class="flex-auto p-2">
                        <h6 class="mb-3"><i class="bi bi-person"></i> {{ $bonCommande->customer_name }}</h6>
                        <ul class="list-group list-group-flush mb-0">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('boncommande.date') }}</span><span>{{ $bonCommande->date }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('boncommande.total') }}</span><span>{{ format_currency($bonCommande->total_amount) }}</span></li>
                            @if($bonCommande->quotation)
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('boncommande.devis') }}</span><a href="{{ route('quotations.show', $bonCommande->quotation_id) }}">{{ $bonCommande->quotation->reference }}</a></li>
                            @endif
                        </ul>
                    </div>
                    <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
                        @include('boncommande.partials.actions', ['data' => $bonCommande])
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('boncommande.none-found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $bonCommandes->links('pagination::bootstrap-5') }}
    </div>
</div>
