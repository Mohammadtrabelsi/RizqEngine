<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ExpenseCategoryController;
use App\Http\Controllers\Api\ProductCatalogController;
use App\Http\Controllers\Api\Reports\InventoryValuationController;
use App\Http\Controllers\Api\Reports\LowStockController;
use App\Http\Controllers\Api\Reports\PaymentsReportController;
use App\Http\Controllers\Api\Reports\ProductMovementController;
use App\Http\Controllers\Api\Reports\ProfitLossController;
use App\Http\Controllers\Api\Reports\PurchasesReportController;
use App\Http\Controllers\Api\Reports\PurchasesReturnReportController;
use App\Http\Controllers\Api\Reports\SalesReportController;
use App\Http\Controllers\Api\Reports\SalesReturnReportController;
use App\Http\Controllers\Api\Reports\StockMovementController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\StockExitController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UnitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Controllers in App\Http\Controllers\Api are reserved exclusively for the
| API: every public service method under App\Services has a matching endpoint
| here. Controllers hold no business logic — they validate input, call the
| service and return JSON. The interactive (web) UI is served by Livewire.
|
*/

Route::middleware('auth:api')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());

    // CategoryService
    Route::get('categories/next-code', [CategoryController::class, 'nextCode']);
    Route::apiResource('categories', CategoryController::class);

    // CurrencyService
    Route::apiResource('currencies', CurrencyController::class)->except('show');

    // CustomerService
    Route::apiResource('customers', CustomerController::class);

    // SupplierService
    Route::apiResource('suppliers', SupplierController::class);

    // UnitService
    Route::apiResource('units', UnitController::class)->except('show');

    // ExpenseCategoryService
    Route::apiResource('expense-categories', ExpenseCategoryController::class)->except('show');

    // ProductCatalogService
    Route::get('products/search', [ProductCatalogController::class, 'search']);
    Route::get('products', [ProductCatalogController::class, 'index']);
    Route::get('products/{id}', [ProductCatalogController::class, 'show']);

    // StockService
    Route::post('products/{product}/stock-in', [StockController::class, 'stockIn']);
    Route::post('products/{product}/stock-out', [StockController::class, 'stockOut']);
    Route::post('products/{product}/set-quantity', [StockController::class, 'setQuantity']);

    // StockExitService (Sortie-Retour workflow)
    Route::post('stock-exits', [StockExitController::class, 'store']);
    Route::post('stock-exits/{stockExit}/entry', [StockExitController::class, 'storeEntry']);

    // Reports
    Route::prefix('reports')->group(function () {
        // InventoryValuationService
        Route::get('inventory-valuation/totals', [InventoryValuationController::class, 'totals']);
        Route::get('inventory-valuation/products', [InventoryValuationController::class, 'products']);
        Route::get('inventory-valuation/categories', [InventoryValuationController::class, 'categories']);

        // LowStockService
        Route::get('low-stock', [LowStockController::class, 'index']);
        Route::get('low-stock/out-of-stock-count', [LowStockController::class, 'outOfStockCount']);
        Route::get('low-stock/low-stock-count', [LowStockController::class, 'lowStockCount']);

        // PaymentsReportService
        Route::get('payments', [PaymentsReportController::class, 'index']);

        // ProductMovementService
        Route::get('product-movement', [ProductMovementController::class, 'ranked']);

        // ProfitLossService
        Route::get('profit-loss', [ProfitLossController::class, 'summary']);

        // SalesReportService
        Route::get('sales', [SalesReportController::class, 'index']);

        // SalesReturnReportService
        Route::get('sales-returns', [SalesReturnReportController::class, 'index']);

        // PurchasesReportService
        Route::get('purchases', [PurchasesReportController::class, 'index']);

        // PurchasesReturnReportService
        Route::get('purchases-returns', [PurchasesReturnReportController::class, 'index']);

        // StockMovementService
        Route::get('stock-movements', [StockMovementController::class, 'index']);
        Route::get('stock-movements/products', [StockMovementController::class, 'products']);
    });
});
