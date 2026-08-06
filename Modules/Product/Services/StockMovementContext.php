<?php

namespace Modules\Product\Services;

use Modules\Product\Observers\ProductObserver;

/**
 * A tiny per-request holder that lets callers attribute the *next* change to a
 * product's quantity to a business document (a Sale, a Purchase, a manual
 * adjustment, ...).
 *
 * Existing modules mutate `product_quantity` directly; the {@see ProductObserver}
 * turns every one of those changes into a stock-ledger row. When a caller wants
 * the ledger row to be attributed to a source, it sets the context just before
 * saving the product. The observer consumes the context exactly once and then
 * clears it, so it can never leak into an unrelated update.
 */
class StockMovementContext
{
    protected static ?array $pending = null;

    public static function set(?string $type, ?string $referenceType = null, ?int $referenceId = null, ?string $note = null): void
    {
        static::$pending = [
            'type' => $type,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'note' => $note,
        ];
    }

    /**
     * Return the pending attribution (if any) and clear it in the same call.
     */
    public static function pull(): ?array
    {
        $context = static::$pending;
        static::$pending = null;

        return $context;
    }

    public static function clear(): void
    {
        static::$pending = null;
    }
}
