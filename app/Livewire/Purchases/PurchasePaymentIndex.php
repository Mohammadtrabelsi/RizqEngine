<?php

namespace App\Livewire\Purchases;

use App\Models\PurchasePayment;
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

    public function delete(int $id): void
    {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        PurchasePayment::findOrFail($id)->delete();

        session()->flash('warning', trans('purchase.purchase-payment-deleted'));
    }

    public function render()
    {
        $payments = PurchasePayment::query()
            ->where('purchase_id', $this->purchaseId)
            ->when($this->search, fn ($q) => $q->where('reference', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(12);

        return view('livewire.purchases.purchase-payment-index', compact('payments'));
    }
}
