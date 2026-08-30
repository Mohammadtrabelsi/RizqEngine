<?php

namespace App\Livewire\Units;

use App\Services\UnitService;
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

    public function delete(int $id, UnitService $units): void
    {
        $units->delete($id);

        session()->flash('warning', trans('setting.unit-deleted'));
    }

    public function render(UnitService $units)
    {
        return view('livewire.units.unit-index', [
            'units' => $units->paginate($this->search),
        ])->layout('components.layouts.admin', ['title' => __('units.units')]);
    }
}
