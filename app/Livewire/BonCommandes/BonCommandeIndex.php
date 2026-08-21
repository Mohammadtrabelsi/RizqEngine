<?php

namespace App\Livewire\BonCommandes;

use App\Models\BonCommande;
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

    public function mount(): void
    {
        abort_if(Gate::denies('access_bon_commandes'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $bonCommandes = BonCommande::query()
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where('reference', 'like', $term)
                    ->orWhere('customer_name', 'like', $term);
            })
            ->latest()
            ->paginate(12);

        return view('livewire.boncommandes.bon-commande-index', compact('bonCommandes'));
    }
}
