<?php

namespace App\Livewire\StockTransfers;

use App\Models\Warehouse;
use App\Services\StockTransferService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StockTransferIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $warehouseId = '';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingWarehouseId(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['status', 'warehouseId', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function render(StockTransferService $transfers)
    {
        abort_if(Gate::denies('access_stock_transfers'), 403);

        return view('livewire.stock-transfers.stock-transfer-index', [
            'transfers' => $transfers->paginate($this->search, [
                'status' => $this->status,
                'warehouse_id' => $this->warehouseId,
                'date_from' => $this->dateFrom,
                'date_to' => $this->dateTo,
            ]),
            'warehouses' => Warehouse::query()->orderBy('name')->get(),
        ])->layout('components.layouts.admin', ['title' => __('warehouses.stock_transfers')]);
    }
}
