<?php

namespace App\Livewire\Sales;

use App\Models\SalePayment;
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

    public function delete(int $id): void
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        SalePayment::findOrFail($id)->delete();

        session()->flash('warning', trans('sale.sale-payment-deleted'));
    }

    public function render()
    {
        $payments = SalePayment::query()
            ->where('sale_id', $this->saleId)
            ->when($this->search, fn ($q) => $q->where('reference', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(12);

        return view('livewire.sales.sale-payment-index', compact('payments'));
    }
}
