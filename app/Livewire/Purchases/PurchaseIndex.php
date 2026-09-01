<?php

namespace App\Livewire\Purchases;

use App\Services\PurchaseService;
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

    public function render(PurchaseService $purchases)
    {
        return view('livewire.purchases.purchase-index', [
            'purchases' => $purchases->paginate($this->search),
        ]);
    }
}
