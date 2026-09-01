<?php

namespace App\Livewire;

use App\Services\CategoryService;
use App\Services\ProductCatalogService;
use Illuminate\Support\Collection;
use Livewire\Component;

class SearchProduct extends Component
{
    public $query;

    public $search_results;

    public $how_many;

    public $category;

    public $in_stock_only;

    public $categories;

    public function mount(CategoryService $categories)
    {
        $this->query = '';
        $this->how_many = 5;
        $this->category = '';
        $this->in_stock_only = false;
        $this->search_results = Collection::empty();
        $this->categories = $categories->ordered();
    }

    public function render()
    {
        return view('livewire.search-product');
    }

    public function updatedQuery()
    {
        $this->runSearch();
    }

    public function updatedCategory()
    {
        $this->runSearch();
    }

    public function updatedInStockOnly()
    {
        $this->runSearch();
    }

    protected function runSearch()
    {
        if (empty($this->query)) {
            $this->search_results = Collection::empty();

            return;
        }

        $this->search_results = app(ProductCatalogService::class)
            ->search($this->query, $this->how_many, [
                'category' => $this->category ?: null,
                'in_stock_only' => (bool) $this->in_stock_only,
            ]);
    }

    public function loadMore()
    {
        $this->how_many += 5;
        $this->runSearch();
    }

    public function resetQuery()
    {
        $this->query = '';
        $this->how_many = 5;
        $this->category = '';
        $this->in_stock_only = false;
        $this->search_results = Collection::empty();
    }

    public function selectProduct($product)
    {
        $this->dispatch('productSelected', $product);
    }
}
