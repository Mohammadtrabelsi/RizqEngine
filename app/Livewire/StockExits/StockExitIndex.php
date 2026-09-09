<?php

namespace App\Livewire\StockExits;

use App\Services\StockExitService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StockExitIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $kind = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingKind(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
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
        $this->reset(['kind', 'status', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function delete(int $id, StockExitService $stockExits): void
    {
        abort_if(Gate::denies('delete_stock_exits'), 403);

        $stockExits->delete($id);

        session()->flash('warning', trans('stockexit.exit-deleted'));
    }

    public function render(StockExitService $stockExits)
    {
        return view('livewire.stockexits.stock-exit-index', [
            'stockExits' => $stockExits->paginate($this->search, [
                'kind' => $this->kind,
                'status' => $this->status,
                'date_from' => $this->dateFrom,
                'date_to' => $this->dateTo,
            ]),
        ]);
    }
}
