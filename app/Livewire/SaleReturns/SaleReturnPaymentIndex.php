<?php

namespace App\Livewire\SaleReturns;

use App\Services\SaleReturnService;
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

    public function delete(int $id, SaleReturnService $saleReturns): void
    {
        abort_if(Gate::denies('access_sale_return_payments'), 403);

        $saleReturns->deletePayment($id);

        session()->flash('warning', trans('salesreturn.sale-return-payment-deleted'));
    }

    public function render(SaleReturnService $saleReturns)
    {
        return view('livewire.sale-returns.sale-return-payment-index', [
            'payments' => $saleReturns->paginatePayments($this->saleReturnId, $this->search),
        ]);
    }
}
