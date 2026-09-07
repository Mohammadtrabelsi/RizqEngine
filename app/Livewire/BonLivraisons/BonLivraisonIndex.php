<?php

namespace App\Livewire\BonLivraisons;

use App\Services\BonLivraisonService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BonLivraisonIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function mount(): void
    {
        abort_if(Gate::denies('access_bon_livraisons'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(BonLivraisonService $bonLivraisons)
    {
        return view('livewire.bon-livraisons.bon-livraison-index', [
            'bonLivraisons' => $bonLivraisons->paginate($this->search),
        ]);
    }
}
