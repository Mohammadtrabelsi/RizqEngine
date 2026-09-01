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

    public function updatingSearch(): void
    {
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
            'outings' => $outings->paginate($this->search),
        ])->layout('components.layouts.admin', ['title' => __('finance.outings')]);
    }
}
