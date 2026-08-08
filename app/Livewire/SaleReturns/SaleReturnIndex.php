<?php

namespace App\Livewire\SaleReturns;

use App\Models\SaleReturn;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SaleReturnIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function mount(): void
    {
        abort_if(Gate::denies('access_sale_returns'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $sale_returns = SaleReturn::query()
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where('reference', 'like', $term)
                    ->orWhere('customer_name', 'like', $term)
;
            })
            ->latest()
            ->paginate(12);

        return view('livewire.sale-returns.sale-return-index', compact('sale_returns'));
    }
}
