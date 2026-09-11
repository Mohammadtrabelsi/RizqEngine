<?php

namespace App\Livewire\Finance;

use App\Services\Finance\OutingService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class OutingIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public function updatingSearch(): void
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
        $this->reset(['dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function delete(int $id, OutingService $outings): void
    {
        $outings->delete($id);

        session()->flash('warning', __('finance.outing_deleted'));
    }

    public function render(OutingService $outings)
    {
        return view('livewire.finance.outing-index', [
            'outings' => $outings->paginate($this->search, [
                'date_from' => $this->dateFrom,
                'date_to' => $this->dateTo,
            ]),
        ])->layout('components.layouts.admin', ['title' => __('finance.outings')]);
    }
}
