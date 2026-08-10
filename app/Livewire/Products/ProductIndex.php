<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function mount(): void
    {
        abort_if(Gate::denies('access_products'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        abort_if(Gate::denies('delete_products'), 403);

        Product::findOrFail($id)->delete();

        session()->flash('warning', trans('product.product-deleted'));
    }

    public function render()
    {
        $products = Product::query()
            ->with('category', 'supplier')
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where('product_name', 'like', $term)
                    ->orWhere('product_code', 'like', $term);
            })
            ->latest()
            ->paginate(12);

        return view('livewire.products.product-index', compact('products'));
    }
}
