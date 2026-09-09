<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <a href="{{ route('quotations.create') }}" class="btn btn-primary">
                {{ __('quotations.add_quotation') }} <i class="bi bi-plus"></i>
            </a>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $quotations->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($quotations as $quotation)
            <div class="col-xl-4 col-lg-6 mb-4" wire:key="quotation-{{ $quotation->id }}">
                <div class="card h-100">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
                        <span class="fw-bold me-2">{{ $quotation->reference }}</span>
                        @include('quotation.partials.status', ['data' => $quotation])
                    </div>
                    <div class="flex-auto p-2">
                        <h6 class="mb-3"><i class="bi bi-person"></i> {{ $quotation->customer_name }}</h6>
                        <ul class="list-group list-group-flush mb-0">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('quotations.date') }}</span><span>{{ $quotation->date }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('quotations.total') }}</span><span>{{ format_currency($quotation->total_amount) }}</span></li>
                        </ul>
                    </div>
                    <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
                        @include('quotation.partials.actions', ['data' => $quotation])
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('quotations.no_quotations_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $quotations->links('pagination::bootstrap-5') }}
    </div>
</div>
