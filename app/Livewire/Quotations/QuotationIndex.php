<?php

namespace App\Livewire\Quotations;

use App\Models\Quotation;
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

    public function render()
    {
        $quotations = Quotation::query()
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where('reference', 'like', $term)
                    ->orWhere('customer_name', 'like', $term)
;
            })
            ->latest()
            ->paginate(12);

        return view('livewire.quotations.quotation-index', compact('quotations'));
    }
}
