<?php

namespace App\Enums;

/**
 * Lifecycle state of an individually tracked (serialised) unit.
 *
 *  - InStock:  available in inventory.
 *  - Sold:     dispatched to a customer.
 *  - Returned: came back from a customer.
 *  - Scrapped: written off / destroyed.
 */
enum SerialStatus: string
{
    case InStock = 'in_stock';
    case Sold = 'sold';
    case Returned = 'returned';
    case Scrapped = 'scrapped';

    public function label(): string
    {
        return match ($this) {
            self::InStock => 'In stock',
            self::Sold => 'Sold',
            self::Returned => 'Returned',
            self::Scrapped => 'Scrapped',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::InStock => 'success',
            self::Sold => 'primary',
            self::Returned => 'warning',
            self::Scrapped => 'danger',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
