<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a credit sale would push a customer's outstanding balance beyond
 * their approved credit limit. Used to block the sale at the register (caisse)
 * so a customer can never be over-extended.
 */
class CreditLimitExceededException extends RuntimeException
{
    //
}
