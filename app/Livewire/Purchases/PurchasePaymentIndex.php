<?php

namespace App\Livewire\Purchases;

use App\Services\PurchaseService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class PurchasePaymentIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public int $purchaseId;

    public string $search = '';

    public function mount(int $purchaseId): void
    {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $this->purchaseId = $purchaseId;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, PurchaseService $purchases): void
    {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $purchases->deletePayment($id);

        session()->flash('warning', trans('purchase.purchase-payment-deleted'));
    }

    public function render(PurchaseService $purchases)
    {
        return view('livewire.purchases.purchase-payment-index', [
            'payments' => $purchases->paginatePayments($this->purchaseId, $this->search),
        ]);
    }
}
