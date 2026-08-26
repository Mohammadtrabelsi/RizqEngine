<?php

namespace App\Livewire\Products;

use App\Enums\StockStatus;
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

    /** Selected stock status filter ('' = all), one of StockStatus values. */
    #[Url(as: 'stock')]
    public string $stockStatus = '';

    public function mount(): void
    {
        abort_if(Gate::denies('access_products'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStockStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Available stock status options for the filter control.
     *
     * @return array<string, string>
     */
    public function getStockStatusOptionsProperty(): array
    {
        $options = ['' => __('app.all') ?? 'All'];

        foreach (StockStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }

    public function delete(int $id): void
    {
        abort_if(Gate::denies('delete_products'), 403);

        Product::findOrFail($id)->delete();

        session()->flash('warning', trans('product.product-deleted'));
    }

    public function render()
    {
        $status = StockStatus::tryFrom($this->stockStatus);

        $products = Product::query()
            ->with('category', 'supplier')
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('product_name', 'like', $term)
                        ->orWhere('product_code', 'like', $term);
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->stockStatus($status);
            })
            ->latest()
            ->paginate(12);

        return view('livewire.products.product-index', compact('products'));
    }
}
