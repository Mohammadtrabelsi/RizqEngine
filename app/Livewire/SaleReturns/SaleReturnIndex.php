<?php

namespace App\Livewire\SaleReturns;

use App\Services\SaleReturnService;
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

    public function render(SaleReturnService $saleReturns)
    {
        return view('livewire.sale-returns.sale-return-index', [
            'sale_returns' => $saleReturns->paginate($this->search),
        ]);
    }
}
