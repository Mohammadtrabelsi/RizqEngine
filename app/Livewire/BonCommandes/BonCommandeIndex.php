<?php

namespace App\Livewire\BonCommandes;

use App\Services\BonCommandeService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BonCommandeIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public function mount(): void
    {
        abort_if(Gate::denies('access_bon_commandes'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
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
        $this->reset(['status', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function render(BonCommandeService $bonCommandes)
    {
        return view('livewire.boncommandes.bon-commande-index', [
            'bonCommandes' => $bonCommandes->paginate($this->search, [
                'status' => $this->status,
                'date_from' => $this->dateFrom,
                'date_to' => $this->dateTo,
            ]),
        ]);
    }
}
