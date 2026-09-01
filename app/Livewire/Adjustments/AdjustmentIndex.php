<?php

namespace App\Livewire\Adjustments;

use App\Services\AdjustmentService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdjustmentIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, AdjustmentService $adjustments): void
    {
        abort_if(Gate::denies('delete_adjustments'), 403);

        $adjustments->delete($id);

        session()->flash('warning', trans('adjustment.adjustment-deleted'));
    }

    public function render(AdjustmentService $adjustments)
    {
        return view('livewire.adjustments.adjustment-index', [
            'adjustments' => $adjustments->paginate($this->search),
        ]);
    }
}
