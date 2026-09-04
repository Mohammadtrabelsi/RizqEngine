<?php

namespace App\Enums;

/**
 * State of a cash-register (till) session.
 */
enum CashRegisterStatus: string
{
    case Open = 'open';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Closed => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'success',
            self::Closed => 'secondary',
        };
    }
}
