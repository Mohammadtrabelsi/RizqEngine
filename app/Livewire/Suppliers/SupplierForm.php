<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;

class SupplierForm extends Component
{
    use WithFileUploads;

    public ?int $supplierId = null;

    public ?Supplier $supplier = null;

    public string $supplier_name = '';

    public string $supplier_email = '';

    public string $supplier_phone = '';

    public string $city = '';

    public string $country = '';

    public string $address = '';

    public $image;

    public function mount(?Supplier $supplier = null): void
    {
        if ($supplier && $supplier->exists) {
            abort_if(Gate::denies('edit_suppliers'), 403);

            $this->supplier = $supplier;
            $this->supplierId = $supplier->id;
            $this->supplier_name = (string) $supplier->supplier_name;
            $this->supplier_email = (string) $supplier->supplier_email;
            $this->supplier_phone = (string) $supplier->supplier_phone;
            $this->city = (string) $supplier->city;
            $this->country = (string) $supplier->country;
            $this->address = (string) $supplier->address;
        } else {
            abort_if(Gate::denies('create_suppliers'), 403);
        }
    }

    protected function rules(): array
    {
        return [
            'supplier_name' => 'required|string|max:255',
            'supplier_phone' => 'required|max:255',
            'supplier_email' => 'required|email|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function save(SupplierService $suppliers)
    {
        $data = $this->validate();

        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($this->supplierId) {
            abort_if(Gate::denies('edit_suppliers'), 403);
            $suppliers->update($this->supplierId, $data, $image);

            session()->flash('info', trans('people.supplier-updated'));
        } else {
            abort_if(Gate::denies('create_suppliers'), 403);
            $suppliers->create($data, $image);

            session()->flash('success', trans('people.supplier-created'));
        }

        return redirect()->route('suppliers.index');
    }

    public function render()
    {
        return view('livewire.suppliers.supplier-form');
    }
}
