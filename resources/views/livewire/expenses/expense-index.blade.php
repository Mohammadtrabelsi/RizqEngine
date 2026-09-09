<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                {{ __('expense.create') }} <i class="bi bi-plus"></i>
            </a>
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
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('expense.category') }}</label>
                    <select wire:model.live="categoryId" class="form-select">
                        <option value="">{{ __('app.all') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @endforeach
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

    <div class="d-flex justify-content-center mb-3">{{ $expenses->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($expenses as $expense)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="expense-{{ $expense->id }}">
                <div class="card h-100">
                    <div class="flex-auto p-2">
                        <h5 class="card-title">{{ $expense->reference }}</h5>
                        <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-secondary mb-2">{{ optional($expense->category)->category_name }}</span>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('expense.date') }}</span><span>{{ $expense->date }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('expense.amount') }}</span><span>{{ format_currency($expense->amount) }}</span></li>
                            <li class="list-group-item px-0">{{ $expense->details }}</li>
                        </ul>
                        <div class="btn-group">
                            @can('edit_expenses')
                                <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-info btn-sm"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('delete_expenses')
                                <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $expense->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('expense.no_expenses_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $expenses->links('pagination::bootstrap-5') }}
    </div>
</div>
