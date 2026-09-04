<?php

namespace App\Livewire\StockTransfers;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\StockTransferService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class StockTransferForm extends Component
{
    public ?int $from_warehouse_id = null;

    public ?int $to_warehouse_id = null;

    public string $date;

    public string $note = '';

    /** @var array<int, array{product_id: ?int, quantity: int}> */
    public array $lines = [];

    public function mount(): void
    {
        $this->date = now()->toDateString();
        $this->lines = [['product_id' => null, 'quantity' => 1]];
    }

    public function addLine(): void
    {
        $this->lines[] = ['product_id' => null, 'quantity' => 1];
    }

    public function removeLine(int $index): void
    {
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);

        if ($this->lines === []) {
            $this->addLine();
        }
    }

    protected function rules(): array
    {
        return [
            'from_warehouse_id' => 'required|integer|exists:warehouses,id',
            'to_warehouse_id' => 'required|integer|exists:warehouses,id|different:from_warehouse_id',
            'date' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|integer|exists:products,id',
            'lines.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function save(StockTransferService $transfers)
    {
        abort_if(Gate::denies('create_stock_transfers'), 403);

        $this->validate();

        try {
            $transfers->create(
                Warehouse::findOrFail($this->from_warehouse_id),
                Warehouse::findOrFail($this->to_warehouse_id),
                $this->lines,
                $this->date,
                $this->note ?: null,
            );
        } catch (InsufficientStockException $e) {
            $this->addError('lines', $e->getMessage());

            return null;
        }

        session()->flash('success', __('warehouses.transfer_created'));

        return redirect()->route('stock-transfers.index');
    }

    public function render()
    {
        abort_if(Gate::denies('create_stock_transfers'), 403);

        return view('livewire.stock-transfers.stock-transfer-form', [
            'warehouses' => Warehouse::active()->orderBy('name')->get(),
            'products' => Product::orderBy('product_name')->get(['id', 'product_name', 'product_code']),
        ])->layout('components.layouts.admin', ['title' => __('warehouses.add_transfer')]);
    }
}
