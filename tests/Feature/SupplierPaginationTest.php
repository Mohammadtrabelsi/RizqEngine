<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\Paginator;
use Tests\TestCase;

class SupplierPaginationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Suppliers created in the same request share a created_at timestamp, so
     * ordering by created_at alone leaves the sort non-deterministic and rows
     * can repeat or vanish between pages. A stable tiebreaker must keep the
     * pages disjoint.
     */
    public function test_pages_do_not_overlap_when_timestamps_are_identical(): void
    {
        Supplier::factory()->count(24)->create([
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $suppliers = new SupplierService();

        Paginator::currentPageResolver(fn () => 1);
        $page1 = $suppliers->paginate(null, 12)->pluck('id')->all();

        Paginator::currentPageResolver(fn () => 2);
        $page2 = $suppliers->paginate(null, 12)->pluck('id')->all();

        $this->assertCount(12, $page1);
        $this->assertCount(12, $page2);
        $this->assertEmpty(array_intersect($page1, $page2), 'Page 1 and page 2 share rows.');
        $this->assertCount(24, array_unique(array_merge($page1, $page2)));
    }
}
