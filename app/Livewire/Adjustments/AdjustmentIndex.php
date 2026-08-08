<?php

namespace App\Livewire\Adjustments;

use App\Models\Adjustment;
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

    public function delete(int $id): void
    {
        abort_if(Gate::denies('delete_adjustments'), 403);

        Adjustment::findOrFail($id)->delete();

        session()->flash('warning', trans('adjustment.adjustment-deleted'));
    }

    public function render()
    {
        $adjustments = Adjustment::query()
            ->withCount('adjustedProducts')
            ->when($this->search, function ($query) {
                $query->where('reference', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate(12);

        return view('livewire.adjustments.adjustment-index', compact('adjustments'));
    }
}
