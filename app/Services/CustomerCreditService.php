<?php

namespace App\Services;

use App\Exceptions\CreditLimitExceededException;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

/**
 * Manages a customer's credit account (encours client). All amounts are handled
 * in cents to match the sales money columns.
 *
 * The register (caisse) uses {@see self::charge()} when a sale leaves an amount
 * on credit; charging is atomic and pessimistically locked, and it refuses to
 * push the balance past the customer's approved limit — so an over-limit sale
 * is blocked and rolled back before any stock leaves.
 */
class CustomerCreditService
{
    /**
     * Remaining credit head-room for a customer, in cents (never negative).
     */
    public function availableCredit(Customer $customer): int
    {
        return max(0, (int) $customer->credit_limit - (int) $customer->current_balance);
    }

    /**
     * Whether a customer may take on the given additional amount on credit.
     */
    public function canCharge(Customer $customer, int $amountCents): bool
    {
        if ($amountCents <= 0) {
            return true;
        }

        if ((int) $customer->credit_limit <= 0) {
            return false;
        }

        return $this->availableCredit($customer) >= $amountCents;
    }

    /**
     * Add an owed amount to the customer's balance, enforcing the credit limit.
     *
     * @throws CreditLimitExceededException when the charge would exceed the limit.
     */
    public function charge(Customer $customer, int $amountCents, ?callable $describe = null): Customer
    {
        if ($amountCents <= 0) {
            return $customer;
        }

        return DB::transaction(function () use ($customer, $amountCents, $describe) {
            /** @var Customer $locked */
            $locked = Customer::lockForUpdate()->findOrFail($customer->id);

            if ((int) $locked->credit_limit <= 0) {
                throw new CreditLimitExceededException(
                    __('customers.credit_not_allowed', ['name' => $locked->customer_name])
                );
            }

            $newBalance = (int) $locked->current_balance + $amountCents;

            if ($newBalance > (int) $locked->credit_limit) {
                $available = max(0, (int) $locked->credit_limit - (int) $locked->current_balance);

                throw new CreditLimitExceededException(
                    __('customers.credit_limit_exceeded', [
                        'name' => $locked->customer_name,
                        'available' => number_format($available / 100, 2),
                    ])
                );
            }

            $locked->update(['current_balance' => $newBalance]);

            if ($describe !== null) {
                $describe($locked);
            }

            return $locked->refresh();
        });
    }

    /**
     * Reduce the customer's balance when a payment is received (floored at 0).
     */
    public function settle(Customer $customer, int $amountCents): Customer
    {
        if ($amountCents <= 0) {
            return $customer;
        }

        return DB::transaction(function () use ($customer, $amountCents) {
            /** @var Customer $locked */
            $locked = Customer::lockForUpdate()->findOrFail($customer->id);

            $locked->update([
                'current_balance' => max(0, (int) $locked->current_balance - $amountCents),
            ]);

            return $locked->refresh();
        });
    }
}
