<?php

namespace App\Livewire\Units;

use App\Models\Unit;
use Livewire\Component;

class UnitForm extends Component
{
    public ?int $unitId = null;

    public string $name = '';

    public string $short_name = '';

    public string $operator = '';

    public string $operation_value = '';

    public function mount(?Unit $unit = null): void
    {
        if ($unit && $unit->exists) {
            $this->unitId = $unit->id;
            $this->name = (string) $unit->name;
            $this->short_name = (string) $unit->short_name;
            $this->operator = (string) $unit->operator;
            $this->operation_value = (string) $unit->operation_value;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'operator' => 'nullable|string|max:255',
            'operation_value' => 'nullable|string|max:255',
        ];
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->unitId) {
            Unit::findOrFail($this->unitId)->update($data);
            session()->flash('info', trans('setting.unit-updated'));
        } else {
            Unit::create($data);
            session()->flash('success', trans('setting.unit-created'));
        }

        return redirect()->route('units.index');
    }

    public function render()
    {
        return view('livewire.units.unit-form');
    }
}
