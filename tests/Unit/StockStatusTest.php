<?php

namespace Tests\Unit;

use App\Enums\StockStatus;
use PHPUnit\Framework\TestCase;

class StockStatusTest extends TestCase
{
    /** @test */
    public function it_reports_out_of_stock_when_quantity_is_zero_or_less(): void
    {
        $this->assertSame(StockStatus::OutOfStock, StockStatus::fromQuantity(0, 5));
        $this->assertSame(StockStatus::OutOfStock, StockStatus::fromQuantity(-3, 5));
    }

    /** @test */
    public function it_reports_low_stock_at_or_below_the_alert_threshold(): void
    {
        $this->assertSame(StockStatus::LowStock, StockStatus::fromQuantity(1, 5));
        $this->assertSame(StockStatus::LowStock, StockStatus::fromQuantity(5, 5));
    }

    /** @test */
    public function it_reports_in_stock_above_the_alert_threshold(): void
    {
        $this->assertSame(StockStatus::InStock, StockStatus::fromQuantity(6, 5));
        $this->assertSame(StockStatus::InStock, StockStatus::fromQuantity(100, 5));
    }

    /** @test */
    public function each_status_exposes_display_metadata(): void
    {
        $this->assertSame('In Stock', StockStatus::InStock->label());
        $this->assertSame('success', StockStatus::InStock->color());
        $this->assertSame('warning', StockStatus::LowStock->color());
        $this->assertSame('danger', StockStatus::OutOfStock->color());
    }
}
