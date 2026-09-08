<?php

namespace App\Livewire\Warehouses;

use App\Models\StockExit;
use App\Models\Warehouse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * "État des dépôts": a live read-only overview of what each warehouse holds,
 * plus the Bons de Sortie (stock exits) still in transit, so the current state
 * of the stock is visible at a glance.
 */
class WarehouseStockDashboard extends Component
{
    /** Optional warehouse filter (0 = all warehouses). */
    #[Url(as: 'w')]
    public int $warehouseId = 0;

    /** Free-text product search. */
    #[Url(as: 'q')]
    public string $search = '';

    public function render()
    {
        abort_if(Gate::denies('access_warehouses'), 403);

        $warehouses = Warehouse::query()
            ->active()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        $rows = $this->stockRows();

        return view('livewire.warehouses.warehouse-stock-dashboard', [
            'warehouses' => $warehouses,
            'rows' => $rows,
            'totalsByWarehouse' => $rows->groupBy('warehouse_id')->map->sum('quantity'),
            'outstandingExits' => $this->outstandingExits(),
        ])->layout('components.layouts.admin', ['title' => __('warehouses.stock_state')]);
    }

    /**
     * On-hand rows joined across the product_warehouse pivot, filtered by the
     * selected warehouse and product search.
     *
     * @return Collection<int, object>
     */
    protected function stockRows(): Collection
    {
        return DB::table('product_warehouse as pw')
            ->join('products as p', 'p.id', '=', 'pw.product_id')
            ->join('warehouses as w', 'w.id', '=', 'pw.warehouse_id')
            ->when($this->warehouseId > 0, fn ($q) => $q->where('pw.warehouse_id', $this->warehouseId))
            ->when($this->search !== '', function ($q) {
                $term = '%'.$this->search.'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('p.product_name', 'like', $term)
                        ->orWhere('p.product_code', 'like', $term);
                });
            })
            ->where('pw.quantity', '>', 0)
            ->orderBy('w.name')
            ->orderBy('p.product_name')
            ->get([
                'pw.warehouse_id',
                'w.name as warehouse_name',
                'p.id as product_id',
                'p.product_name',
                'p.product_code',
                'p.product_stock_alert',
                'pw.quantity',
            ]);
    }

    /**
     * Bons de Sortie still in transit, with their outstanding quantity.
     *
     * @return Collection<int, StockExit>
     */
    protected function outstandingExits(): Collection
    {
        return StockExit::query()
            ->where('status', StockExit::STATUS_IN_TRANSIT)
            ->with(['details', 'customer'])
            ->latest()
            ->get();
    }
}
