<?php

namespace App\Livewire\PurchaseReturns;

use App\Models\PurchaseReturnPayment;
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

    public function delete(int $id): void
    {
        abort_if(Gate::denies('access_purchase_return_payments'), 403);

        PurchaseReturnPayment::findOrFail($id)->delete();

        session()->flash('warning', trans('purchasesreturn.purchase-return-payment-deleted'));
    }

    public function render()
    {
        $payments = PurchaseReturnPayment::query()
            ->where('purchase_return_id', $this->purchaseReturnId)
            ->when($this->search, fn ($q) => $q->where('reference', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(12);

        return view('livewire.purchase-returns.purchase-return-payment-index', compact('payments'));
    }
}
