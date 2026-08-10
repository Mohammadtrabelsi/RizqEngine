<?php

namespace App\Livewire\StockExits;

use App\Models\StockExit;
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

    public function delete(int $id): void
    {
        abort_if(Gate::denies('delete_stock_exits'), 403);

        StockExit::findOrFail($id)->delete();

        session()->flash('warning', trans('stockexit.exit-deleted'));
    }

    public function render()
    {
        $stockExits = StockExit::query()
            ->withCount('details')
            ->when($this->search, function ($query) {
                $query->where('reference', 'like', '%'.$this->search.'%')
                    ->orWhere('destination', 'like', '%'.$this->search.'%')
                    ->orWhere('responsible', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate(12);

        return view('livewire.stockexits.stock-exit-index', compact('stockExits'));
    }
}
