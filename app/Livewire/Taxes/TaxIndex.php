<?php

namespace App\Livewire\Taxes;

use App\Services\TaxService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TaxIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, TaxService $taxes): void
    {
        $taxes->delete($id);

        session()->flash('warning', trans('taxes.tax-deleted'));
    }

    public function render(TaxService $taxes)
    {
        return view('livewire.taxes.tax-index', [
            'taxes' => $taxes->paginate($this->search),
        ])->layout('components.layouts.admin', ['title' => __('taxes.taxes')]);
    }
}
