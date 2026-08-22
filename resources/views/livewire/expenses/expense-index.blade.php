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

    <div class="d-flex justify-content-center mb-3">{{ $expenses->links() }}</div>
    <div class="row">
        @forelse($expenses as $expense)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="expense-{{ $expense->id }}">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $expense->reference }}</h5>
                        <span class="badge bg-secondary mb-2">{{ optional($expense->category)->category_name }}</span>
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
                <div class="card"><div class="card-body text-center text-muted">{{ __('expense.no_expenses_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $expenses->links() }}
    </div>
</div>
