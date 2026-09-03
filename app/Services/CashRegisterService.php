<?php

namespace App\Services;

use App\Enums\CashRegisterStatus;
use App\Models\CashRegisterSession;
use App\Models\SalePayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Manages cashier till sessions: opening with a float (fond de caisse), closing
 * with a counted amount, and producing the expected-cash and difference (écart)
 * figures for the daily Z report. All amounts are handled in cents.
 */
class CashRegisterService
{
    /**
     * Payment-method labels treated as cash for the register reconciliation.
     *
     * @var string[]
     */
    public const CASH_METHODS = ['cash', 'espèces', 'especes', 'cash payment'];

    /**
     * The user's currently open session, if any.
     */
    public function currentFor(User $user): ?CashRegisterSession
    {
        return CashRegisterSession::open()->where('user_id', $user->id)->latest('opened_at')->first();
    }

    /**
     * Open a new session for a cashier with a starting float.
     *
     * @throws \RuntimeException when the user already has an open session.
     */
    public function open(User $user, int $openingFloatCents, ?int $warehouseId = null, ?string $note = null): CashRegisterSession
    {
        if ($openingFloatCents < 0) {
            throw new \InvalidArgumentException('The opening float cannot be negative.');
        }

        return DB::transaction(function () use ($user, $openingFloatCents, $warehouseId, $note) {
            if ($this->currentFor($user) !== null) {
                throw new \RuntimeException(__('cash_register.already_open'));
            }

            return CashRegisterSession::create([
                'user_id' => $user->id,
                'warehouse_id' => $warehouseId,
                'opening_float' => $openingFloatCents,
                'status' => CashRegisterStatus::Open,
                'opened_at' => now(),
                'note' => $note,
            ]);
        });
    }

    /**
     * Cash taken in during the session window (cents).
     */
    public function cashSales(CashRegisterSession $session): int
    {
        $until = $session->closed_at ?? now();

        return (int) SalePayment::query()
            ->where('created_by', $session->user_id)
            ->whereBetween('created_at', [$session->opened_at, $until])
            ->whereIn(DB::raw('LOWER(payment_method)'), self::CASH_METHODS)
            ->sum('amount');
    }

    /**
     * Expected cash in the drawer: opening float + cash sales (cents).
     */
    public function expectedCash(CashRegisterSession $session): int
    {
        return (int) $session->opening_float + $this->cashSales($session);
    }

    /**
     * Close a session against the physically counted amount, recording the
     * expected total and the difference (counted − expected).
     *
     * @throws \RuntimeException when the session is already closed.
     */
    public function close(CashRegisterSession $session, int $countedAmountCents, ?string $note = null): CashRegisterSession
    {
        return DB::transaction(function () use ($session, $countedAmountCents, $note) {
            /** @var CashRegisterSession $locked */
            $locked = CashRegisterSession::lockForUpdate()->findOrFail($session->id);

            if (! $locked->isOpen()) {
                throw new \RuntimeException(__('cash_register.already_closed'));
            }

            $expected = $this->expectedCash($locked);

            $locked->update([
                'closing_amount' => $countedAmountCents,
                'expected_amount' => $expected,
                'difference' => $countedAmountCents - $expected,
                'status' => CashRegisterStatus::Closed,
                'closed_at' => now(),
                'note' => $note ?? $locked->note,
            ]);

            return $locked->refresh();
        });
    }

    /**
     * Data for the daily Z report of a closed session.
     *
     * @return array{opening_float:int, cash_sales:int, expected:int, counted:int, difference:int}
     */
    public function zReport(CashRegisterSession $session): array
    {
        return [
            'opening_float' => (int) $session->opening_float,
            'cash_sales' => $this->cashSales($session),
            'expected' => (int) ($session->expected_amount ?? $this->expectedCash($session)),
            'counted' => (int) $session->closing_amount,
            'difference' => (int) $session->difference,
        ];
    }
}
