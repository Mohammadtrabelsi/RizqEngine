<div>
    @include('utils.alerts')
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#categoryCreateModal">
                Add Category <i class="bi bi-plus"></i>
            </button>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="row">
        @forelse($categories as $category)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="category-{{ $category->id }}">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="{{ default_category_image() }}" alt="{{ $category->category_name }}" class="img-thumbnail mb-2" width="80" height="80" style="object-fit: cover;">
                        <h5 class="card-title mb-1">{{ $category->category_name }}</h5>
                        <p class="text-muted mb-2"><small>{{ $category->category_code }}</small></p>
                        <span class="badge bg-info mb-3">{{ $category->products_count }} products</span>
                        <div class="btn-group d-block">
                            <a href="{{ route('product-categories.edit', $category->id) }}" class="btn btn-info btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $category->id }})" wire:confirm="{{ __('app.are_you_sure') }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-muted">{{ __('product.no_categories_found') }}</div></div>
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
                    <h5 class="modal-title" id="categoryCreateModalLabel">Create Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <livewire:product-categories.category-form/>
                </div>
            </div>
        </div>
    </div>
</div>
