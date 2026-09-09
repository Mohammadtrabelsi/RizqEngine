<?php

namespace App\Livewire\Commandes;

use App\Services\CommandeService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CommandeIndex extends Component
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
        abort_if(Gate::denies('access_commandes'), 403);
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

    public function render(CommandeService $commandes)
    {
        return view('livewire.commandes.commande-index', [
            'commandes' => $commandes->paginate($this->search, [
                'status' => $this->status,
                'date_from' => $this->dateFrom,
                'date_to' => $this->dateTo,
            ]),
        ]);
    }
}
