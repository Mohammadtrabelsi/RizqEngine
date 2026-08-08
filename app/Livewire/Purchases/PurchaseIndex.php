<?php

namespace App\Livewire\Purchases;

use App\Models\Purchase;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function mount(): void
    {
        abort_if(Gate::denies('access_purchases'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $purchases = Purchase::query()
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where('reference', 'like', $term)
                    ->orWhere('supplier_name', 'like', $term);
            })
            ->latest()
            ->paginate(12);

        return view('livewire.purchases.purchase-index', compact('purchases'));
    }
}
