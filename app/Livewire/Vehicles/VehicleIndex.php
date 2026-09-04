<?php

namespace App\Livewire\Vehicles;

use App\Services\VehicleService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class VehicleIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, VehicleService $vehicles): void
    {
        abort_if(Gate::denies('delete_vehicles'), 403);

        $vehicles->delete($id);

        session()->flash('warning', trans('vehicles.vehicle-deleted'));
    }

    public function render(VehicleService $vehicles)
    {
        abort_if(Gate::denies('access_vehicles'), 403);

        return view('livewire.vehicles.vehicle-index', [
            'vehicles' => $vehicles->paginate($this->search),
        ])->layout('components.layouts.admin', ['title' => __('vehicles.vehicles')]);
    }
}
