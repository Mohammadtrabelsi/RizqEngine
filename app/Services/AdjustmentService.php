<?php

namespace App\Services;

use App\Models\AdjustedProduct;
use App\Models\Adjustment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

/**
 * Owns stock-adjustment operations. Each adjusted line moves stock through
 * {@see StockService} (reference_type=Adjustment) so manual corrections are
 * locked and recorded in the ledger alongside every other movement.
 */
class AdjustmentService
{
    public function __construct(private readonly StockService $stock) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function createAdjustment(array $data): Adjustment
    {
        return DB::transaction(function () use ($data) {
            $adjustment = Adjustment::create([
                'date' => $data['date'],
                'note' => $data['note'] ?? null,
            ]);

            $this->applyLines($adjustment, $data);

            return $adjustment;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateAdjustment(Adjustment $adjustment, array $data): Adjustment
    {
        return DB::transaction(function () use ($adjustment, $data) {
            $adjustment->update([
                'reference' => $data['reference'],
                'date' => $data['date'],
                'note' => $data['note'] ?? null,
            ]);

            foreach ($adjustment->adjustedProducts as $adjustedProduct) {
                $this->reverseLine($adjustment, $adjustedProduct);
                $adjustedProduct->delete();
            }

            $this->applyLines($adjustment, $data);

            return $adjustment;
        });
    }

    /**
     * Persist each adjusted line and apply its stock effect.
     *
     * @param  array<string, mixed>  $data
     */
    private function applyLines(Adjustment $adjustment, array $data): void
    {
        foreach ($data['product_ids'] as $key => $id) {
            $quantity = (int) $data['quantities'][$key];
            $type = $data['types'][$key];

            AdjustedProduct::create([
                'adjustment_id' => $adjustment->id,
                'product_id' => $id,
                'quantity' => $data['quantities'][$key],
                'type' => $type,
            ]);

            if ($quantity <= 0) {
                continue;
            }

            $product = Product::findOrFail($id);

            if ($type == 'add') {
                $this->stock->stockIn($product, $quantity, null, 'Adjustment', $adjustment->id);
            } elseif ($type == 'sub') {
                $this->stock->stockOut($product, $quantity, null, 'Adjustment', $adjustment->id);
            }
        }
    }

    /**
     * Undo the stock effect of a previously stored adjusted line.
     */
    private function reverseLine(Adjustment $adjustment, AdjustedProduct $adjustedProduct): void
    {
        $quantity = (int) $adjustedProduct->quantity;

        if ($quantity <= 0) {
            return;
        }

        $product = Product::findOrFail($adjustedProduct->product->id);

        if ($adjustedProduct->type == 'add') {
            $this->stock->stockOut($product, $quantity, null, 'Adjustment', $adjustment->id);
        } elseif ($adjustedProduct->type == 'sub') {
            $this->stock->stockIn($product, $quantity, null, 'Adjustment', $adjustment->id);
        }
    }
}
