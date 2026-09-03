<?php

namespace App\Enums;

/**
 * Lifecycle state of a stock transfer between two warehouses.
 *
 *  - Draft:     recorded but stock has not yet been moved.
 *  - Completed: stock has been moved from the source to the destination.
 *  - Cancelled: the transfer was voided (any moved stock reversed).
 */
enum TransferStatus: string
{
    case Draft = 'draft';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'secondary',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }
}
