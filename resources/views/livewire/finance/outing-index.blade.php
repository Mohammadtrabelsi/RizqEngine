{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <a href="{{ route('outings.create') }}" class="btn btn-primary">{{ __('finance.add_outing') }} <i class="bi bi-plus"></i></a>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
            </div>
        </div>

        {{-- Filters container --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                <h5 class="mb-0"><i class="bi bi-funnel text-primary"></i> {{ __('app.filters') }}</h5>
            </div>
            <div class="flex-auto p-2">
                <div class="row align-items-end">
                    <div class="col-12 col-lg-4 mb-3">
                        <label class="form-label small text-muted mb-1">{{ __('finance.start_date') }}</label>
                        <input type="date" wire:model.live="dateFrom" class="form-control">
                    </div>
                    <div class="col-12 col-lg-4 mb-3">
                        <label class="form-label small text-muted mb-1">{{ __('finance.end_date') }}</label>
                        <input type="date" wire:model.live="dateTo" class="form-control">
                    </div>
                    <div class="col-12 col-lg-4 mb-3">
                        <button type="button" wire:click="resetFilters" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> {{ __('app.reset') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mb-3">{{ $outings->links('pagination::bootstrap-5') }}</div>
        <div class="row">
            @forelse($outings as $outing)
                <div class="col-xl-4 col-lg-6 mb-4" wire:key="outing-{{ $outing->id }}">
                    <div class="card h-100">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
                            <span class="fw-bold">{{ $outing->reference }}</span>
                            <span class="text-muted small">{{ $outing->date->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex-auto p-2">
                            <h6 class="mb-3"><i class="bi bi-geo-alt"></i> {{ $outing->location }}</h6>
                            <ul class="list-group list-group-flush mb-0">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('finance.purpose') }}</span><span>{{ $outing->purpose }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0 fw-semibold"><span>{{ __('finance.total') }}</span><span>{{ number_format($outing->total(), 2) }}</span></li>
                            </ul>
                        </div>
                        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                @if($outing->hasVoucher())
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($outing->voucher_path) }}" target="_blank" class="btn btn-sm btn-secondary" title="{{ __('finance.voucher') }}">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> {{ __('finance.voucher') }}
                                    </a>
                                @endif
                                <a href="{{ route('outings.edit', $outing) }}" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-pencil me-1 text-primary"></i> {{ __('app.edit') }}
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $outing->id }})" wire:confirm="{{ __('app.are_you_sure') }}">
                                    <i class="bi bi-trash me-1"></i> {{ __('app.delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('finance.no_outings') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-3">{{ $outings->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
