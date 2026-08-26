<?php

namespace App\Enums;

/**
 * Derived stock state for a product, based on its on-hand quantity relative
 * to its configured alert threshold.
 *
 *  - OutOfStock: no quantity remaining (quantity <= 0)
 *  - LowStock:   some quantity, but at or below the alert threshold
 *  - InStock:    quantity above the alert threshold
 */
enum StockStatus: string
{
    case InStock = 'in_stock';
    case LowStock = 'low_stock';
    case OutOfStock = 'out_of_stock';

    /**
     * Resolve the status from an on-hand quantity and its alert threshold.
     */
    public static function fromQuantity(int $quantity, int $alert): self
    {
        return match (true) {
            $quantity <= 0 => self::OutOfStock,
            $quantity <= $alert => self::LowStock,
            default => self::InStock,
        };
    }

    /**
     * Human-readable label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::InStock => 'In Stock',
            self::LowStock => 'Low Stock',
            self::OutOfStock => 'Out of Stock',
        };
    }

    /**
     * Bootstrap contextual colour used for badges/tags.
     */
    public function color(): string
    {
        return match ($this) {
            self::InStock => 'success',
            self::LowStock => 'warning',
            self::OutOfStock => 'danger',
        };
    }

    /**
     * Bootstrap icon name associated with the status.
     */
    public function icon(): string
    {
        return match ($this) {
            self::InStock => 'check-circle',
            self::LowStock => 'exclamation-triangle',
            self::OutOfStock => 'x-circle',
        };
    }
}
