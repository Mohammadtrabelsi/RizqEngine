<?php

namespace App\Livewire\Drivers;

use App\Services\DriverService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DriverIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, DriverService $drivers): void
    {
        abort_if(Gate::denies('delete_drivers'), 403);

        $drivers->delete($id);

        session()->flash('warning', trans('drivers.driver-deleted'));
    }

    public function render(DriverService $drivers)
    {
        abort_if(Gate::denies('access_drivers'), 403);

        return view('livewire.drivers.driver-index', [
            'drivers' => $drivers->paginate($this->search),
        ])->layout('components.layouts.admin', ['title' => __('drivers.drivers')]);
    }
}
