<?php

namespace Modules\Product\Exceptions;

use RuntimeException;

/**
 * Thrown when a stock-out operation would drive a product's on-hand quantity
 * below zero. Used to keep inventory in a consistent state.
 */
class InsufficientStockException extends RuntimeException
{
    //
}
