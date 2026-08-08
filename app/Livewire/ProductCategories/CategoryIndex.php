<?php

namespace App\Livewire\ProductCategories;

use App\Models\Category;
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

    public function delete(int $id): void
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        $category = Category::findOrFail($id);

        if ($category->products()->exists()) {
            session()->flash('error', "Can't delete because there are products associated with this category.");

            return;
        }

        $category->delete();

        session()->flash('warning', trans('product.product-category-deleted'));
    }

    public function render()
    {
        $categories = Category::query()
            ->withCount('products')
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where('category_name', 'like', $term)
                    ->orWhere('category_code', 'like', $term);
            })
            ->latest()
            ->paginate(12);

        return view('livewire.product-categories.category-index', compact('categories'));
    }
}
