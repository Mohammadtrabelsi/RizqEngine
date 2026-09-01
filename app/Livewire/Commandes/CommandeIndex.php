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

    public function mount(): void
    {
        abort_if(Gate::denies('access_commandes'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(CommandeService $commandes)
    {
        return view('livewire.commandes.commande-index', [
            'commandes' => $commandes->paginate($this->search),
        ]);
    }
}
