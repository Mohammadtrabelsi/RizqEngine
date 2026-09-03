<?php

namespace App\Livewire\Warehouses;

use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class WarehouseForm extends Component
{
    public ?int $warehouseId = null;

    public string $name = '';

    public string $code = '';

    public string $phone = '';

    public string $city = '';

    public string $address = '';

    public bool $is_default = false;

    public bool $is_active = true;

    public string $note = '';

    public function mount(?Warehouse $warehouse = null): void
    {
        if ($warehouse && $warehouse->exists) {
            $this->warehouseId = $warehouse->id;
            $this->name = (string) $warehouse->name;
            $this->code = (string) $warehouse->code;
            $this->phone = (string) $warehouse->phone;
            $this->city = (string) $warehouse->city;
            $this->address = (string) $warehouse->address;
            $this->is_default = (bool) $warehouse->is_default;
            $this->is_active = (bool) $warehouse->is_active;
            $this->note = (string) $warehouse->note;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:255', Rule::unique('warehouses', 'code')->ignore($this->warehouseId)],
            'phone' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'note' => 'nullable|string|max:1000',
        ];
    }

    public function save(WarehouseService $warehouses)
    {
        abort_if(Gate::denies($this->warehouseId ? 'edit_warehouses' : 'create_warehouses'), 403);

        $data = $this->validate();

        if ($this->warehouseId) {
            $warehouses->update($this->warehouseId, $data);
            session()->flash('info', __('warehouses.warehouse_updated'));
        } else {
            $warehouses->create($data);
            session()->flash('success', __('warehouses.warehouse_created'));
        }

        return redirect()->route('warehouses.index');
    }

    public function render()
    {
        abort_if(Gate::denies($this->warehouseId ? 'edit_warehouses' : 'create_warehouses'), 403);

        return view('livewire.warehouses.warehouse-form')
            ->layout('components.layouts.admin', ['title' => $this->warehouseId ? __('warehouses.edit_warehouse') : __('warehouses.add_warehouse')]);
    }
}
