<?php

namespace App\Livewire\Warehouses;

use App\Services\WarehouseService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class WarehouseIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, WarehouseService $warehouses): void
    {
        abort_if(Gate::denies('delete_warehouses'), 403);

        $warehouses->delete($id);

        session()->flash('warning', __('warehouses.warehouse_deleted'));
    }

    public function render(WarehouseService $warehouses)
    {
        abort_if(Gate::denies('access_warehouses'), 403);

        return view('livewire.warehouses.warehouse-index', [
            'warehouses' => $warehouses->paginate($this->search),
        ])->layout('components.layouts.admin', ['title' => __('warehouses.warehouses')]);
    }
}
