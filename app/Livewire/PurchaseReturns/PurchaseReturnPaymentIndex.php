<?php

namespace App\Livewire\PurchaseReturns;

use App\Services\PurchaseReturnService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseReturnPaymentIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public int $purchaseReturnId;

    public string $search = '';

    public function mount(int $purchaseReturnId): void
    {
        abort_if(Gate::denies('access_purchase_return_payments'), 403);

        $this->purchaseReturnId = $purchaseReturnId;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, PurchaseReturnService $purchaseReturns): void
    {
        abort_if(Gate::denies('access_purchase_return_payments'), 403);

        $purchaseReturns->deletePayment($id);

        session()->flash('warning', trans('purchasesreturn.purchase-return-payment-deleted'));
    }

    public function render(PurchaseReturnService $purchaseReturns)
    {
        return view('livewire.purchase-returns.purchase-return-payment-index', [
            'payments' => $purchaseReturns->paginatePayments($this->purchaseReturnId, $this->search),
        ]);
    }
}
