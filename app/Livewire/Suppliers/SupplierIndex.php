<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierIndex extends Component
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
        abort_if(Gate::denies('delete_suppliers'), 403);

        Supplier::findOrFail($id)->delete();

        session()->flash('warning', trans('people.supplier-deleted'));
    }

    public function render()
    {
        $suppliers = Supplier::query()
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where('supplier_name', 'like', $term)
                    ->orWhere('supplier_email', 'like', $term)
                    ->orWhere('supplier_phone', 'like', $term);
            })
            ->latest()
            ->paginate(12);

        return view('livewire.suppliers.supplier-index', compact('suppliers'));
    }
}
