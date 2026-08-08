<div>
    @include('utils.alerts')
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#categoryCreateModal">
                {{ __('expense-category.add_category') }} <i class="bi bi-plus"></i>
            </button>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="row">
        @forelse($categories as $category)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="expense-category-{{ $category->id }}">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $category->category_name }}</h5>
                        <span class="badge bg-info mb-2">{{ $category->expenses_count }} expenses</span>
                        <p class="text-muted">{{ $category->category_description }}</p>
                        <div class="btn-group">
                            <a href="{{ route('expense-categories.edit', $category->id) }}" class="btn btn-info btn-sm"><i class="bi bi-pencil"></i></a>
                            <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $category->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
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
</div>
