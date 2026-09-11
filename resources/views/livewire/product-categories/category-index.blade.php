<div>
    @include('utils.alerts')
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <a href="{{ route('product-categories.create') }}" class="btn btn-primary">
                Add Category <i class="bi bi-plus"></i>
            </a>
            @can('access_product_categories')
                <a href="{{ route('product-categories.import') }}" class="btn btn-outline">
                    {{ __('import.categories') }} <i class="bi bi-upload"></i>
                </a>
            @endcan
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $categories->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($categories as $category)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="category-{{ $category->id }}">
                <div class="card h-100">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                        <h5 class="mb-0">
                            @if ($category->color)
                                <span class="d-inline-block rounded-circle align-middle me-1" style="width: 12px; height: 12px; background-color: {{ $category->color }};"></span>
                            @endif
                            {{ $category->category_name }}
                            <small class="text-muted fw-normal">{{ $category->category_code }}</small>
                        </h5>
                    </div>
                    <div class="flex-auto p-2 p-3 text-center">
                        <img src="{{ $category->image_url }}" alt="{{ $category->category_name }}" class="thumb-cover img-thumbnail mb-2" width="80" height="80">
                        @if ($category->description)
                            <p class="text-muted mb-2"><small>{{ \Illuminate\Support\Str::limit($category->description, 60) }}</small></p>
                        @endif
                        <div class="mb-3">
                            <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-info">{{ $category->products_count }} products</span>
                            @if ($category->is_active)
                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-success">{{ __('product.category_active') }}</span>
                            @else
                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-secondary">{{ __('product.category_inactive') }}</span>
                            @endif
                        </div>
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
                <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('product.no_categories_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $categories->links('pagination::bootstrap-5') }}
    </div>
</div>
