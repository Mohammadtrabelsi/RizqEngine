<?php

namespace App\Livewire\Units;

use App\Models\Unit;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UnitIndex extends Component
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
        Unit::findOrFail($id)->delete();

        session()->flash('warning', trans('setting.unit-deleted'));
    }

    public function render()
    {
        $units = Unit::query()
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where('name', 'like', $term)
                    ->orWhere('short_name', 'like', $term);
            })
            ->orderByDesc('id')
            ->paginate(12);

        return view('livewire.units.unit-index', compact('units'));
    }
}
