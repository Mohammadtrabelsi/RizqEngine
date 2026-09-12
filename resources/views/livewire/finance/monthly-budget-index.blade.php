{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('monthly-budgets.create') }}" class="btn btn-primary">
                    {{ __('finance.add_budget') }} <i class="bi bi-plus"></i>
                </a>
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
                        <label class="form-label small text-muted mb-1">{{ __('finance.year') }}</label>
                        <select wire:model.live="year" class="form-select">
                            <option value="">{{ __('app.all') }}</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-4 mb-3">
                        <label class="form-label small text-muted mb-1">{{ __('finance.month') }}</label>
                        <select wire:model.live="month" class="form-select">
                            <option value="">{{ __('app.all') }}</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}">{{ \Illuminate\Support\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-4 mb-3">
                        <button type="button" wire:click="resetFilters" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> {{ __('app.reset') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @forelse($budgets as $budget)
                <div class="col-xl-4 col-lg-6 mb-4" wire:key="budget-{{ $budget->id }}">
                    <div class="card h-100">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $budget->label() }}</h5>
                            @php($remaining = $budget->remainingBalance())
                            <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline {{ $remaining < 0 ? 'bg-danger' : 'bg-success' }}">
                                {{ number_format($remaining, 2) }}
                            </span>
                        </div>
                        <div class="flex-auto p-2">
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('finance.starting_budget') }}</span><span>{{ number_format($budget->starting_budget, 2) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('finance.total_fixed') }}</span><span>{{ number_format($budget->totalFixedPayments(), 2) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('finance.total_outings') }}</span><span>{{ number_format($budget->totalOutings(), 2) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0 fw-semibold"><span>{{ __('finance.remaining_balance') }}</span><span class="{{ $remaining < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($remaining, 2) }}</span></li>
                            </ul>
                        </div>
                        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 text-center">
                            <div class="btn-group">
                                <a href="{{ route('monthly-budgets.show', $budget) }}" class="btn btn-outline btn-sm"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('monthly-budgets.edit', $budget) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $budget->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('finance.no_budgets_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">{{ $budgets->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
