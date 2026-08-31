<?php

namespace App\Livewire\PurchaseReturns;

use App\Services\PurchaseReturnService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseReturnIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function mount(): void
    {
        abort_if(Gate::denies('access_purchase_returns'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(PurchaseReturnService $purchaseReturns)
    {
        return view('livewire.purchase-returns.purchase-return-index', [
            'purchase_returns' => $purchaseReturns->paginate($this->search),
        ]);
    }
}
