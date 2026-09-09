<?php

namespace App\Livewire\PurchaseReturns;

use App\Services\PurchaseReturnService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseReturnIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $paymentStatus = '';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public function mount(): void
    {
        abort_if(Gate::denies('access_purchase_returns'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPaymentStatus(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['status', 'paymentStatus', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function render(PurchaseReturnService $purchaseReturns)
    {
        return view('livewire.purchase-returns.purchase-return-index', [
            'purchase_returns' => $purchaseReturns->paginate($this->search, [
                'status' => $this->status,
                'payment_status' => $this->paymentStatus,
                'date_from' => $this->dateFrom,
                'date_to' => $this->dateTo,
            ]),
        ]);
    }
}
