<?php

namespace App\Services;

/**
 * Resolves the payment status of a financial document (sale, purchase,
 * sale return, purchase return) from its outstanding and total amounts.
 *
 * This is the single source of truth for a rule that was previously copied
 * verbatim into every document and payment controller.
 */
class PaymentStatusService
{
    public const UNPAID = 'Unpaid';

    public const PARTIAL = 'Partial';

    public const PAID = 'Paid';

    /**
     * @param  float|int  $dueAmount  Outstanding amount after the payment/document.
     * @param  float|int  $totalAmount  Document grand total.
     *
     * Both amounts must be expressed in the same unit (the codebase compares
     * accessor-normalised amounts, i.e. major currency units).
     */
    public function resolve(float|int $dueAmount, float|int $totalAmount): string
    {
        if ($dueAmount == $totalAmount) {
            return self::UNPAID;
        }

        if ($dueAmount > 0) {
            return self::PARTIAL;
        }

        return self::PAID;
    }
}
