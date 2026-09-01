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

        <div class="row">
            @forelse($budgets as $budget)
                <div class="col-xl-4 col-lg-6 mb-4" wire:key="budget-{{ $budget->id }}">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $budget->label() }}</h5>
                                @php($remaining = $budget->remainingBalance())
                                <span class="badge {{ $remaining < 0 ? 'bg-danger' : 'bg-success' }}">
                                    {{ number_format($remaining, 2) }}
                                </span>
                            </div>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('finance.starting_budget') }}</span><span>{{ number_format($budget->starting_budget, 2) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('finance.total_fixed') }}</span><span>{{ number_format($budget->totalFixedPayments(), 2) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('finance.total_outings') }}</span><span>{{ number_format($budget->totalOutings(), 2) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0 fw-semibold"><span>{{ __('finance.remaining_balance') }}</span><span class="{{ $remaining < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($remaining, 2) }}</span></li>
                            </ul>
                            <div class="btn-group">
                                <a href="{{ route('monthly-budgets.show', $budget) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('monthly-budgets.edit', $budget) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $budget->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">{{ __('finance.no_budgets_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">{{ $budgets->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
