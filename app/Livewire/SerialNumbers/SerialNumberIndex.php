<?php

namespace App\Livewire\SerialNumbers;

use App\Enums\SerialStatus;
use App\Models\Product;
use App\Services\SerialNumberService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SerialNumberIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    public ?int $product_id = null;

    public string $serial = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function register(SerialNumberService $serials): void
    {
        abort_if(Gate::denies('access_serial_numbers'), 403);

        $this->validate([
            'product_id' => 'required|integer|exists:products,id',
            'serial' => 'required|string|max:255|unique:serial_numbers,serial',
        ]);

        $serials->create(['product_id' => $this->product_id, 'serial' => $this->serial]);

        $this->reset('serial');
        session()->flash('success', __('batches.serial_registered'));
    }

    public function changeStatus(int $id, string $status, SerialNumberService $serials): void
    {
        abort_if(Gate::denies('access_serial_numbers'), 403);

        $serials->changeStatus($id, SerialStatus::from($status));
    }

    public function render(SerialNumberService $serials)
    {
        abort_if(Gate::denies('access_serial_numbers'), 403);

        return view('livewire.serial-numbers.serial-number-index', [
            'serials' => $serials->paginate($this->search, $this->statusFilter ?: null),
            'products' => Product::orderBy('product_name')->get(['id', 'product_name', 'product_code']),
            'statuses' => SerialStatus::cases(),
        ])->layout('components.layouts.admin', ['title' => __('batches.serial_numbers')]);
    }
}
