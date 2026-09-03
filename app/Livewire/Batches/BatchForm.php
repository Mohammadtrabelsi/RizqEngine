<?php

namespace App\Livewire\Batches;

use App\Models\Batch;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\BatchService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class BatchForm extends Component
{
    public ?int $batchId = null;

    public ?int $product_id = null;

    public ?int $warehouse_id = null;

    public string $batch_number = '';

    public int $quantity = 0;

    public ?string $manufactured_date = null;

    public ?string $expiry_date = null;

    public string $note = '';

    public function mount(?Batch $batch = null): void
    {
        if ($batch && $batch->exists) {
            $this->batchId = $batch->id;
            $this->product_id = $batch->product_id;
            $this->warehouse_id = $batch->warehouse_id;
            $this->batch_number = (string) $batch->batch_number;
            $this->quantity = (int) $batch->quantity;
            $this->manufactured_date = $batch->manufactured_date?->toDateString();
            $this->expiry_date = $batch->expiry_date?->toDateString();
            $this->note = (string) $batch->note;
        }
    }

    protected function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'warehouse_id' => 'nullable|integer|exists:warehouses,id',
            'batch_number' => [
                'required', 'string', 'max:255',
                Rule::unique('batches', 'batch_number')
                    ->where(fn ($q) => $q->where('product_id', $this->product_id))
                    ->ignore($this->batchId),
            ],
            'quantity' => 'required|integer|min:0',
            'manufactured_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:manufactured_date',
            'note' => 'nullable|string|max:1000',
        ];
    }

    public function save(BatchService $batches)
    {
        abort_if(Gate::denies($this->batchId ? 'edit_batches' : 'create_batches'), 403);

        $data = $this->validate();

        if ($this->batchId) {
            $batches->update($this->batchId, $data);
            session()->flash('info', __('batches.batch_updated'));
        } else {
            $batches->create($data);
            session()->flash('success', __('batches.batch_created'));
        }

        return redirect()->route('batches.index');
    }

    public function render()
    {
        abort_if(Gate::denies($this->batchId ? 'edit_batches' : 'create_batches'), 403);

        return view('livewire.batches.batch-form', [
            'products' => Product::orderBy('product_name')->get(['id', 'product_name', 'product_code']),
            'warehouses' => Warehouse::active()->orderBy('name')->get(['id', 'name', 'code']),
        ])->layout('components.layouts.admin', ['title' => $this->batchId ? __('batches.edit_batch') : __('batches.add_batch')]);
    }
}
