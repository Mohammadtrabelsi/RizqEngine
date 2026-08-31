<?php

namespace App\Livewire\Pos;

use App\Services\ProductCatalogService;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'selectedCategory' => 'categoryChanged',
        'showCount' => 'showCountChanged',
    ];

    public $categories;

    public $category_id;

    public $limit = 9;

    public function mount($categories)
    {
        $this->categories = $categories;
        $this->category_id = '';
    }

    public function render(ProductCatalogService $service)
    {
        return view('livewire.pos.product-list', [
            'products' => $service->paginateByCategory($this->category_id, $this->limit),
        ]);
    }

    public function categoryChanged($category_id)
    {
        $this->category_id = $category_id;
        $this->resetPage();
    }

    public function showCountChanged($value)
    {
        $this->limit = (int) $value ?: 9;
        $this->resetPage();
    }

    public function selectProduct($product)
    {
        $this->dispatch('productSelected', $product);
    }
}
