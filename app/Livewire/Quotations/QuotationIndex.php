<?php

namespace App\Livewire\Quotations;

use App\Services\QuotationService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class QuotationIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function mount(): void
    {
        abort_if(Gate::denies('access_quotations'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(QuotationService $quotations)
    {
        return view('livewire.quotations.quotation-index', [
            'quotations' => $quotations->paginate($this->search),
        ]);
    }
}
