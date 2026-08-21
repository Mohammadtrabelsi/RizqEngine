<?php

namespace App\Livewire\Commandes;

use App\Models\Commande;
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

    public function render()
    {
        $commandes = Commande::query()
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where('reference', 'like', $term)
                    ->orWhere('customer_name', 'like', $term);
            })
            ->latest()
            ->paginate(12);

        return view('livewire.commandes.commande-index', compact('commandes'));
    }
}
