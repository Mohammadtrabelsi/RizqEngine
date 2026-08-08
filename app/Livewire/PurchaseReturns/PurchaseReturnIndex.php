<?php

namespace App\Livewire\PurchaseReturns;

use App\Models\PurchaseReturn;
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

    public function render()
    {
        $purchase_returns = PurchaseReturn::query()
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where('reference', 'like', $term)
                    ->orWhere('supplier_name', 'like', $term)
;
            })
            ->latest()
            ->paginate(12);

        return view('livewire.purchase-returns.purchase-return-index', compact('purchase_returns'));
    }
}
