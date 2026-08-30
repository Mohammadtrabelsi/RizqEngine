<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a document conversion is not allowed by the workflow rules —
 * e.g. attempting to convert a Devis that has already produced a Bon de
 * Commande, converting a cancelled document, or invoicing a Commande twice.
 * Keeps the Devis → Bon de Commande → Commande → Facture chain consistent.
 */
class ConversionException extends RuntimeException
{
    //
}
