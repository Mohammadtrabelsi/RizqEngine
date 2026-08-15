<?php

namespace Tests\Unit;

use App\Services\PaymentStatusService;
use PHPUnit\Framework\TestCase;

class PaymentStatusServiceTest extends TestCase
{
    private PaymentStatusService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new PaymentStatusService;
    }

    /** @test */
    public function nothing_paid_is_unpaid()
    {
        // Due equals total => no payment has been made yet.
        $this->assertSame('Unpaid', $this->service->resolve(100, 100));
    }

    /** @test */
    public function some_outstanding_balance_is_partial()
    {
        $this->assertSame('Partial', $this->service->resolve(40, 100));
    }

    /** @test */
    public function nothing_outstanding_is_paid()
    {
        $this->assertSame('Paid', $this->service->resolve(0, 100));
    }

    /** @test */
    public function an_overpayment_is_still_paid()
    {
        // A negative due amount (overpaid) must not be mistaken for Partial.
        $this->assertSame('Paid', $this->service->resolve(-10, 100));
    }
}
