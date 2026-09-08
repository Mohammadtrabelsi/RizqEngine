<div>
    @include('utils.alerts')
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700" data-toggle="modal" data-target="#categoryCreateModal">
                Add Category <i class="bi bi-plus"></i>
            </button>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $categories->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($categories as $category)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="category-{{ $category->id }}">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 h-100">
                    <div class="flex-auto p-2 text-center">
                        <img src="{{ $category->image_url }}" alt="{{ $category->category_name }}" class="thumb-cover img-thumbnail mb-2" width="80" height="80">
                        <h5 class="mb-3 text-lg font-semibold text-slate-900 mb-1">
                            @if ($category->color)
                                <span class="d-inline-block rounded-circle align-middle me-1" style="width: 12px; height: 12px; background-color: {{ $category->color }};"></span>
                            @endif
                            {{ $category->category_name }}
                        </h5>
                        <p class="text-muted mb-2"><small>{{ $category->category_code }}</small></p>
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
                            <a href="{{ route('product-categories.edit', $category->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-cyan-500 !text-white border-cyan-500 hover:bg-cyan-600 !px-3 !py-1.5 !text-xs">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-red-500 !text-white border-red-500 hover:bg-red-600 hover:border-red-600 !px-3 !py-1.5 !text-xs" wire:click="delete({{ $category->id }})" wire:confirm="{{ __('app.are_you_sure') }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900"><div class="flex-auto p-2 text-center text-muted">{{ __('product.no_categories_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $categories->links('pagination::bootstrap-5') }}
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
