<?php

namespace App\Livewire\ProductCategories;

use App\Services\CategoryService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, CategoryService $categories): void
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        if (! $categories->delete($id)) {
            session()->flash('error', "Can't delete because there are products associated with this category.");

            return;
        }

        session()->flash('warning', trans('product.product-category-deleted'));
    }

    public function render(CategoryService $categories)
    {
        return view('livewire.product-categories.category-index', [
            'categories' => $categories->paginate($this->search),
        ]);
    }
}
