<?php

namespace App\Livewire\Products;

use App\Enums\StockStatus;
use App\Services\CategoryService;
use App\Services\ProductCatalogService;
use App\Services\SupplierService;
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
        return app(ProductCatalogService::class)->expiredInStockCount();
    }

    /**
     * Manager action: force every expired product that still has stock to
     * zero quantity, moving them to the "out of stock" status.
     */
    public function markExpiredOutOfStock(ProductCatalogService $products): void
    {
        abort_if(Gate::denies('edit_products'), 403);

        $affected = $products->markExpiredOutOfStock();

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
        return app(CategoryService::class)->ordered();
    }

    /**
     * Suppliers available for the filter control.
     */
    public function getSuppliersProperty()
    {
        return app(SupplierService::class)->ordered();
    }

    public function delete(int $id, ProductCatalogService $products): void
    {
        abort_if(Gate::denies('delete_products'), 403);

        $products->delete($id);

        session()->flash('warning', trans('product.product-deleted'));
    }

    public function render(ProductCatalogService $products)
    {
        return view('livewire.products.product-index', [
            'products' => $products->paginateIndex([
                'search' => $this->search,
                'stock_status' => StockStatus::tryFrom($this->stockStatus),
                'category_id' => $this->categoryId,
                'supplier_id' => $this->supplierId,
                'min_price' => $this->minPrice,
                'max_price' => $this->maxPrice,
                'expiry' => $this->expiry,
            ]),
        ]);
    }
}
