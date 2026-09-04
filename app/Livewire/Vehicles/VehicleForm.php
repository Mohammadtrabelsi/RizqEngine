<?php

namespace App\Livewire\Vehicles;

use App\Models\Vehicle;
use App\Services\VehicleService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class VehicleForm extends Component
{
    public ?int $vehicleId = null;

    public string $registration = '';

    public string $brand = '';

    public string $model = '';

    public string $note = '';

    public function mount(?Vehicle $vehicle = null): void
    {
        if ($vehicle && $vehicle->exists) {
            $this->vehicleId = $vehicle->id;
            $this->registration = (string) $vehicle->registration;
            $this->brand = (string) $vehicle->brand;
            $this->model = (string) $vehicle->model;
            $this->note = (string) $vehicle->note;
        }
    }

    protected function rules(): array
    {
        return [
            'registration' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
        ];
    }

    public function save(VehicleService $vehicles)
    {
        abort_if(Gate::denies($this->vehicleId ? 'edit_vehicles' : 'create_vehicles'), 403);

        $data = $this->validate();

        if ($this->vehicleId) {
            $vehicles->update($this->vehicleId, $data);
            session()->flash('info', trans('vehicles.vehicle-updated'));
        } else {
            $vehicles->create($data);
            session()->flash('success', trans('vehicles.vehicle-created'));
        }

        return redirect()->route('vehicles.index');
    }

    public function render()
    {
        abort_if(Gate::denies($this->vehicleId ? 'edit_vehicles' : 'create_vehicles'), 403);

        return view('livewire.vehicles.vehicle-form')
            ->layout('components.layouts.admin', ['title' => $this->vehicleId ? __('vehicles.edit_vehicle') : __('vehicles.add_vehicle')]);
    }
}
