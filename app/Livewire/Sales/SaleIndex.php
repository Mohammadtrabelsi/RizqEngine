<?php

namespace App\Livewire\Sales;

use App\Services\SaleService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SaleIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function mount(): void
    {
        abort_if(Gate::denies('access_sales'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(SaleService $sales)
    {
        return view('livewire.sales.sale-index', [
            'sales' => $sales->paginate($this->search),
        ]);
    }
}
