<?php

namespace App\Livewire\SaleReturns;

use App\Models\SaleReturnPayment;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class SaleReturnPaymentIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public int $saleReturnId;

    public string $search = '';

    public function mount(int $saleReturnId): void
    {
        abort_if(Gate::denies('access_sale_return_payments'), 403);

        $this->saleReturnId = $saleReturnId;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        abort_if(Gate::denies('access_sale_return_payments'), 403);

        SaleReturnPayment::findOrFail($id)->delete();

        session()->flash('warning', trans('salesreturn.sale-return-payment-deleted'));
    }

    public function render()
    {
        $payments = SaleReturnPayment::query()
            ->where('sale_return_id', $this->saleReturnId)
            ->when($this->search, fn ($q) => $q->where('reference', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(12);

        return view('livewire.sale-returns.sale-return-payment-index', compact('payments'));
    }
}
