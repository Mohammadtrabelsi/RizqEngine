<?php

namespace App\Livewire\WithholdingTaxes;

use App\Services\WithholdingTaxService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class WithholdingTaxIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, WithholdingTaxService $withholdingTaxes): void
    {
        $deleted = $withholdingTaxes->deleteOrDeactivate($id);

        session()->flash(
            $deleted ? 'warning' : 'info',
            $deleted ? trans('withholding.withholding-deleted') : trans('withholding.withholding-deactivated'),
        );
    }

    public function render(WithholdingTaxService $withholdingTaxes)
    {
        return view('livewire.withholding-taxes.withholding-tax-index', [
            'withholdingTaxes' => $withholdingTaxes->paginate($this->search),
        ])->layout('components.layouts.admin', ['title' => __('withholding.withholding_taxes')]);
    }
}
