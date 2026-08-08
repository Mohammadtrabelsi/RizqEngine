@extends('layouts.app')

@section('title', __('expense.expenses'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('expense.expenses') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                    {{ __('expense.create') }} <i class="bi bi-plus"></i>
                </a>
            </div>
        </div>

        <div class="row">
            @forelse($expenses as $expense)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
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
                                @include('expense.expenses.partials.actions', ['data' => $expense])
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
@endsection
