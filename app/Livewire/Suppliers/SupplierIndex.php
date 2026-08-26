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

    #[Url(as: 'city')]
    public string $city = '';

    #[Url(as: 'country')]
    public string $country = '';

    /** Filter by presence of a tax id: '' = all, 'yes', 'no'. */
    #[Url(as: 'tax')]
    public string $hasTaxId = '';

    public function mount(): void
    {
        abort_if(Gate::denies('access_suppliers'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCity(): void
    {
        $this->resetPage();
    }

    public function updatingCountry(): void
    {
        $this->resetPage();
    }

    public function updatingHasTaxId(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'city', 'country', 'hasTaxId']);
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
            'suppliers' => $suppliers->paginate($this->search, 12, [
                'city' => $this->city,
                'country' => $this->country,
                'hasTaxId' => $this->hasTaxId,
            ]),
            'cities' => $suppliers->cities(),
            'countries' => $suppliers->countries(),
        ]);
    }
}
