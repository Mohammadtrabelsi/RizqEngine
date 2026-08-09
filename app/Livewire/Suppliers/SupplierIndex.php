<?php

namespace App\Livewire\Suppliers;

use App\Services\SupplierService;
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

    public function delete(int $id, SupplierService $suppliers): void
    {
        abort_if(Gate::denies('delete_suppliers'), 403);

        $suppliers->delete($id);

        session()->flash('warning', trans('people.supplier-deleted'));
    }

    public function render(SupplierService $suppliers)
    {
        return view('livewire.suppliers.supplier-index', [
            'suppliers' => $suppliers->paginate($this->search),
        ]);
    }
}
