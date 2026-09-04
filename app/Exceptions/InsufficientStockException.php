<?php

namespace App\Exceptions;

use App\Services\StockService;
use RuntimeException;

/**
 * Thrown when a stock-out operation would drive a product's on-hand quantity
 * below the enforced minimum ({@see StockService::MINIMUM_STOCK}).
 * Used to keep inventory in a consistent state.
 */
class InsufficientStockException extends RuntimeException
{
    //
}
