<?php

namespace App\Livewire\Sales;

use App\Services\SaleService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class SalePaymentIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public int $saleId;

    public string $search = '';

    public function mount(int $saleId): void
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        $this->saleId = $saleId;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, SaleService $sales): void
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        $sales->deletePayment($id);

        session()->flash('warning', trans('sale.sale-payment-deleted'));
    }

    public function render(SaleService $sales)
    {
        return view('livewire.sales.sale-payment-index', [
            'payments' => $sales->paginatePayments($this->saleId, $this->search),
        ]);
    }
}
