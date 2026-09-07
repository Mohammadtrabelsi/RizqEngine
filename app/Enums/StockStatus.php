<?php

namespace App\Enums;

/**
 * Derived stock state for a product, based on its on-hand quantity relative
 * to its configured alert thresholds.
 *
 *  - OutOfStock: no quantity remaining (quantity <= 0)
 *  - LowStock:   some quantity, but at or below the low-stock alert threshold
 *  - HighStock:  quantity above a configured high-stock (overstock) threshold
 *  - InStock:    quantity above the low-stock threshold and at/below any
 *                configured high-stock threshold
 */
enum StockStatus: string
{
    case InStock = 'in_stock';
    case LowStock = 'low_stock';
    case HighStock = 'high_stock';
    case OutOfStock = 'out_of_stock';

    /**
     * Resolve the status from an on-hand quantity and its alert thresholds.
     * $max is the optional overstock threshold; when null, high-stock is
     * never signalled.
     */
    public static function fromQuantity(int $quantity, int $alert, ?int $max = null): self
    {
        return match (true) {
            $quantity <= 0 => self::OutOfStock,
            $quantity <= $alert => self::LowStock,
            $max !== null && $quantity > $max => self::HighStock,
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
            self::HighStock => 'High Stock',
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
            self::HighStock => 'info',
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
            self::HighStock => 'arrow-up-circle',
            self::OutOfStock => 'x-circle',
        };
    }
}
