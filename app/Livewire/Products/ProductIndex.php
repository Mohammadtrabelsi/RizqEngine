<?php

namespace App\Livewire\Products;

use App\Enums\StockStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
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

    /** Selected category filter ('' = all). */
    #[Url(as: 'category')]
    public string $categoryId = '';

    /** Selected supplier filter ('' = all). */
    #[Url(as: 'supplier')]
    public string $supplierId = '';

    /** Minimum product price filter (in the store currency). */
    #[Url(as: 'min_price')]
    public string $minPrice = '';

    /** Maximum product price filter (in the store currency). */
    #[Url(as: 'max_price')]
    public string $maxPrice = '';

    /** Expiry filter ('' = all, 'expired', 'expiring_soon', 'not_expired'). */
    #[Url(as: 'expiry')]
    public string $expiry = '';

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

    public function updatingCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatingSupplierId(): void
    {
        $this->resetPage();
    }

    public function updatingMinPrice(): void
    {
        $this->resetPage();
    }

    public function updatingMaxPrice(): void
    {
        $this->resetPage();
    }

    public function updatingExpiry(): void
    {
        $this->resetPage();
    }

    /**
     * Reset every filter back to its default (all) value.
     */
    public function resetFilters(): void
    {
        $this->reset(['search', 'stockStatus', 'categoryId', 'supplierId', 'minPrice', 'maxPrice', 'expiry']);
        $this->resetPage();
    }

    /**
     * Expiry filter options for the filter control.
     *
     * @return array<string, string>
     */
    public function getExpiryOptionsProperty(): array
    {
        return [
            '' => __('app.all'),
            'expired' => __('product.expired'),
            'expiring_soon' => __('product.expiring_soon'),
            'not_expired' => __('product.not_expired'),
        ];
    }

    /**
     * Number of currently expired products still holding stock — surfaced so
     * the manager can be shown/hidden and the count communicated to the user.
     */
    public function getExpiredInStockCountProperty(): int
    {
        return Product::query()->expired()->where('product_quantity', '>', 0)->count();
    }

    /**
     * Manager action: force every expired product that still has stock to
     * zero quantity, moving them to the "out of stock" status.
     */
    public function markExpiredOutOfStock(): void
    {
        abort_if(Gate::denies('edit_products'), 403);

        $affected = Product::query()
            ->expired()
            ->where('product_quantity', '>', 0)
            ->update(['product_quantity' => 0]);

        $this->resetPage();

        if ($affected > 0) {
            session()->flash('success', trans('product.expired-marked-out-of-stock', ['count' => $affected]));
        } else {
            session()->flash('info', trans('product.no-expired-products'));
        }
    }

    /**
     * Available stock status options for the filter control.
     *
     * @return array<string, string>
     */
    public function getStockStatusOptionsProperty(): array
    {
        $options = ['' => __('app.all')];

        foreach (StockStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }

    /**
     * Categories available for the filter control.
     */
    public function getCategoriesProperty()
    {
        return Category::orderBy('category_name')->get();
    }

    /**
     * Suppliers available for the filter control.
     */
    public function getSuppliersProperty()
    {
        return Supplier::orderBy('supplier_name')->get();
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
            ->when($this->categoryId !== '', function ($query) {
                $query->where('category_id', $this->categoryId);
            })
            ->when($this->supplierId !== '', function ($query) {
                $query->where('supplier_id', $this->supplierId);
            })
            ->when(is_numeric($this->minPrice), function ($query) {
                // Prices are persisted in cents (see Product::$product_price accessor).
                $query->where('product_price', '>=', (int) round(((float) $this->minPrice) * 100));
            })
            ->when(is_numeric($this->maxPrice), function ($query) {
                $query->where('product_price', '<=', (int) round(((float) $this->maxPrice) * 100));
            })
            ->when($this->expiry === 'expired', fn ($query) => $query->expired())
            ->when($this->expiry === 'expiring_soon', fn ($query) => $query->expiringSoon())
            ->when($this->expiry === 'not_expired', function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('expiry_date')
                        ->orWhereDate('expiry_date', '>', now()->toDateString());
                });
            })
            ->latest()
            ->paginate(12);

        return view('livewire.products.product-index', compact('products'));
    }
}
