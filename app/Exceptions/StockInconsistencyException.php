<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a Bon d'Entrée would return more units than the referenced
 * Bon de Sortie ever took out — an "Incohérence de Stock". The operation is
 * blocked so the ledger can never gain phantom stock.
 */
class StockInconsistencyException extends RuntimeException
{
    //
}
