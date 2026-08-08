@extends('layouts.app')

@section('title', __('expense-category.categories'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">{{ __('expense.expenses') }}</a></li>
        <li class="breadcrumb-item active">{{ __('expense-category.categories') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        @include('utils.alerts')
        <div class="row">
            <div class="col-12 mb-3">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#categoryCreateModal">
                    {{ __('expense-category.add_category') }} <i class="bi bi-plus"></i>
                </button>
            </div>
        </div>

        <div class="row">
            @forelse($categories as $category)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $category->category_name }}</h5>
                            <span class="badge bg-info mb-2">{{ $category->expenses_count }} expenses</span>
                            <p class="text-muted">{{ $category->category_description }}</p>
                            <div class="btn-group">
                                @include('expense::categories.partials.actions', ['data' => $category])
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">{{ __('expense-category.no_categories_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $categories->links() }}
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="categoryCreateModal" tabindex="-1" role="dialog" aria-labelledby="categoryCreateModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryCreateModalLabel">{{ __('expense-category.create') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('expense-categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="category_name">{{ __('expense-category.category_name') }} <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="category_name" required>
                        </div>
                        <div class="form-group">
                            <label for="category_description">{{ __('expense-category.description') }}</label>
                            <textarea class="form-control" name="category_description" id="category_description" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('app.create') }} <i class="bi bi-check"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
