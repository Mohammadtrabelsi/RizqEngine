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

    public function updatingSearch(): void
    {
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
            'stockExits' => $stockExits->paginate($this->search),
        ]);
    }
}
