<?php

namespace App\Livewire\Drivers;

use App\Models\Driver;
use App\Services\DriverService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class DriverForm extends Component
{
    public ?int $driverId = null;

    public string $name = '';

    public string $phone = '';

    public string $license_number = '';

    public string $note = '';

    public function mount(?Driver $driver = null): void
    {
        if ($driver && $driver->exists) {
            $this->driverId = $driver->id;
            $this->name = (string) $driver->name;
            $this->phone = (string) $driver->phone;
            $this->license_number = (string) $driver->license_number;
            $this->note = (string) $driver->note;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
        ];
    }

    public function save(DriverService $drivers)
    {
        abort_if(Gate::denies($this->driverId ? 'edit_drivers' : 'create_drivers'), 403);

        $data = $this->validate();

        if ($this->driverId) {
            $drivers->update($this->driverId, $data);
            session()->flash('info', trans('drivers.driver-updated'));
        } else {
            $drivers->create($data);
            session()->flash('success', trans('drivers.driver-created'));
        }

        return redirect()->route('drivers.index');
    }

    public function render()
    {
        abort_if(Gate::denies($this->driverId ? 'edit_drivers' : 'create_drivers'), 403);

        return view('livewire.drivers.driver-form')
            ->layout('components.layouts.admin', ['title' => $this->driverId ? __('drivers.edit_driver') : __('drivers.add_driver')]);
    }
}
