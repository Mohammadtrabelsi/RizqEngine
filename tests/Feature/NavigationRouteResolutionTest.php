<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Guards against web/API route-name collisions.
 *
 * The API routes (routes/api.php) are registered under the "api." name
 * prefix in RouteServiceProvider. If that prefix is ever dropped, the API
 * resource names (currencies.index, units.index, expense-categories.*, ...)
 * collide with the web/Livewire route names and route() starts resolving the
 * navigation links to the JSON /api/* endpoints instead of the full-page UI.
 */
class NavigationRouteResolutionTest extends TestCase
{
    /**
     * Every navigation route name must resolve to its web page, never /api/*.
     */
    public function test_navigation_links_resolve_to_web_pages_not_api(): void
    {
        $names = [
            'currencies.index' => '/currencies',
            'units.index' => '/units',
            'expense-categories.index' => '/expense-categories',
            'customers.index' => '/parties/customers',
            'suppliers.index' => '/parties/suppliers',
            'products.index' => '/products',
        ];

        foreach ($names as $name => $expectedPath) {
            $path = route($name, [], false);

            $this->assertStringStartsNotWith('/api/', $path, "Route [{$name}] must not point to the API.");
            $this->assertSame($expectedPath, $path, "Route [{$name}] resolved to an unexpected path.");
        }
    }

    /**
     * The API resources remain reachable under their prefixed "api." names.
     */
    public function test_api_routes_are_registered_under_the_api_name_prefix(): void
    {
        $this->assertSame('/api/currencies', route('api.currencies.index', [], false));
        $this->assertSame('/api/units', route('api.units.index', [], false));
        $this->assertSame('/api/expense-categories', route('api.expense-categories.index', [], false));
    }
}
