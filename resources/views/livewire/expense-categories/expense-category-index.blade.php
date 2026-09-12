{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        @include('utils.alerts')
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <a href="{{ route('expense-categories.create') }}" class="btn btn-primary">
                    {{ __('expense-category.add_category') }} <i class="bi bi-plus"></i>
                </a>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
            </div>
        </div>

        <div class="d-flex justify-content-center mb-3">{{ $categories->links('pagination::bootstrap-5') }}</div>
        <div class="row">
            @forelse($categories as $category)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="expense-category-{{ $category->id }}">
                    <div class="card h-100">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                            <h5 class="mb-0">{{ $category->category_name }}</h5>
                        </div>
                        <div class="flex-auto p-2">
                            <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-info mb-2">{{ $category->expenses_count }} expenses</span>
                            <p class="text-muted">{{ $category->category_description }}</p>
                        </div>
                        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 text-center">
                            <div class="btn-group">
                                <a href="{{ route('expense-categories.edit', $category->id) }}" class="btn btn-info btn-sm"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $category->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('expense-category.no_categories_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
