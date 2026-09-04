<?php

namespace App\Livewire\StockTransfers;

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

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(StockTransferService $transfers)
    {
        abort_if(Gate::denies('access_stock_transfers'), 403);

        return view('livewire.stock-transfers.stock-transfer-index', [
            'transfers' => $transfers->paginate($this->search),
        ])->layout('components.layouts.admin', ['title' => __('warehouses.stock_transfers')]);
    }
}
