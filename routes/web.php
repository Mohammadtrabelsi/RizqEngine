<?php

use App\Http\Middleware\SetLocale;
use App\Livewire\Auth\Login as RedesignLogin;
use App\Livewire\Currencies\CurrencyForm;
use App\Livewire\Currencies\CurrencyIndex;
use App\Livewire\Customers\CustomerForm;
use App\Livewire\Customers\CustomerIndex;
use App\Livewire\Customers\CustomerShow;
use App\Livewire\Dashboard as RedesignDashboard;
use App\Livewire\LandingPage as RedesignLandingPage;
use App\Livewire\Suppliers\SupplierForm;
use App\Livewire\Suppliers\SupplierIndex;
use App\Livewire\Suppliers\SupplierShow;
use App\Livewire\Units\UnitForm;
use App\Livewire\Units\UnitIndex;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Quotation;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public front page — the Triangle POS landing screen.
// Namespace reset (\\) so the class-string action is not prefixed with the
// controller namespace configured in RouteServiceProvider.
Route::group(['namespace' => '\\'], function () {
    Route::get('/', RedesignLandingPage::class)->name('welcome');
});

Route::get('/language/{locale}', function (string $locale) {
    if (in_array($locale, SetLocale::SUPPORTED_LOCALES, true)) {
        session()->put('locale', $locale);
    }

    return redirect()->back();
})->name('language.switch');

/*
|--------------------------------------------------------------------------
| Redesign screens (landing / sign-in / dashboard)
|--------------------------------------------------------------------------
| Modern redesign from the design handoff. Mounted on dedicated routes so the
| existing welcome / auth / home screens keep working unchanged.
*/
Route::group(['namespace' => '\\'], function () {
    Route::get('/landing', RedesignLandingPage::class)->name('redesign.landing');

    Route::middleware('guest')->group(function () {
        Route::get('/sign-in', RedesignLogin::class)->name('redesign.login');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', RedesignDashboard::class)->name('dashboard');
    });
});

Auth::routes(['register' => false]);

// Route the framework login screen to the Triangle POS sign-in. Registered
// after Auth::routes so it wins URI matching for GET /login; the POST /login
// handler and the other auth routes stay intact.
Route::redirect('/login', '/sign-in')->name('login');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', 'HomeController@index')
        ->name('home');

    Route::get('/sales-purchases/chart-data', 'HomeController@salesPurchasesChart')
        ->name('sales-purchases.chart');

    Route::get('/current-month/chart-data', 'HomeController@currentMonthChart')
        ->name('current-month.chart');

    Route::get('/payment-flow/chart-data', 'HomeController@paymentChart')
        ->name('payment-flow.chart');

    Route::get('/documentation', 'DocumentationController@index')
        ->name('documentation.index');
});

/*
|--------------------------------------------------------------------------
| Module Routes (migrated from nwidart modules)
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'auth'], function () {
    // Print Barcode
    Route::get('/products/print-barcode', 'BarcodeController@printBarcode')->name('barcode.print');
    // Product
    Route::resource('products', 'ProductController');
    // Product Category
    Route::resource('product-categories', 'CategoriesController')->except('create', 'show');
});

// Parties — customers & suppliers management (full-page Livewire UI under the /parties prefix).
// Route names are kept unprefixed (customers.*, suppliers.*) for backward compatibility.
Route::group(['middleware' => 'auth', 'prefix' => 'parties', 'namespace' => '\\'], function () {
    // Customers (full-page Livewire components)
    Route::get('customers', CustomerIndex::class)->name('customers.index');
    Route::get('customers/create', CustomerForm::class)->name('customers.create');
    Route::get('customers/{customer}', CustomerShow::class)->name('customers.show');
    Route::get('customers/{customer}/edit', CustomerForm::class)->name('customers.edit');

    // Suppliers (full-page Livewire components)
    Route::get('suppliers', SupplierIndex::class)->name('suppliers.index');
    Route::get('suppliers/create', SupplierForm::class)->name('suppliers.create');
    Route::get('suppliers/{supplier}', SupplierShow::class)->name('suppliers.show');
    Route::get('suppliers/{supplier}/edit', SupplierForm::class)->name('suppliers.edit');
});

Route::group(['middleware' => 'auth'], function () {
    Route::group(['namespace' => '\\'], function () {
        // Currencies (full-page Livewire components)
        Route::get('/currencies', CurrencyIndex::class)->name('currencies.index');
        Route::get('/currencies/create', CurrencyForm::class)->name('currencies.create');
        Route::get('/currencies/{currency}/edit', CurrencyForm::class)->name('currencies.edit');
    });
});

Route::group(['middleware' => 'auth'], function () {
    // Mail Settings
    Route::patch('/settings/smtp', 'SettingController@updateSmtp')->name('settings.smtp.update');
    // General Settings
    Route::get('/settings', 'SettingController@index')->name('settings.index');
    Route::patch('/settings', 'SettingController@update')->name('settings.update');
    Route::group(['namespace' => '\\'], function () {
        // Units (full-page Livewire components)
        Route::get('/units', UnitIndex::class)->name('units.index');
        Route::get('/units/create', UnitForm::class)->name('units.create');
        Route::get('/units/{unit}/edit', UnitForm::class)->name('units.edit');
    });
});

Route::group(['middleware' => 'auth'], function () {
    // Expense Category
    Route::resource('expense-categories', 'ExpenseCategoriesController')->except('show', 'create');
    // Expense
    Route::resource('expenses', 'ExpenseController')->except('show');
});

Route::group(['middleware' => 'auth'], function () {
    // Product Adjustment
    Route::resource('adjustments', 'AdjustmentController');
});

Route::group(['middleware' => 'auth'], function () {
    // Sortie-Retour: Bon de Sortie (BS)
    Route::resource('stock-exits', 'StockExitController')
        ->parameters(['stock-exits' => 'stockExit'])
        ->only(['index', 'create', 'store', 'show', 'destroy']);

    // Sortie-Retour: Bon d'Entrée (BE) — always tied to an origin BS
    Route::get('stock-exits/{stockExit}/return', 'StockEntryController@create')->name('stock-entries.create');
    Route::post('stock-exits/{stockExit}/return', 'StockEntryController@store')->name('stock-entries.store');
    Route::get('stock-entries/{stockEntry}', 'StockEntryController@show')->name('stock-entries.show');
});

Route::group(['middleware' => 'auth'], function () {
    // Generate PDF
    Route::get('/purchases/pdf/{id}', function ($id) {
        $purchase = Purchase::findOrFail($id);
        $supplier = Supplier::findOrFail($purchase->supplier_id);
        $pdf = PDF::loadView('purchase.print', [
            'purchase' => $purchase,
            'supplier' => $supplier,
        ])->setPaper('a4');

        return $pdf->stream('purchase-'.$purchase->reference.'.pdf');
    })->name('purchases.pdf');
    // Sales
    Route::resource('purchases', 'PurchaseController');
    // Payments
    Route::group(['prefix' => 'purchase-payments', 'as' => 'purchase-payments.'], function () {
        Route::get('/{purchase_id}', 'PurchasePaymentsController@index')->name('index');
        Route::get('/{purchase_id}/create', 'PurchasePaymentsController@create')->name('create');
        Route::post('/store', 'PurchasePaymentsController@store')->name('store');
        Route::get('/{purchase_id}/edit/{purchasePayment}', 'PurchasePaymentsController@edit')->name('edit');
        Route::patch('/update/{purchasePayment}', 'PurchasePaymentsController@update')->name('update');
        Route::delete('/destroy/{purchasePayment}', 'PurchasePaymentsController@destroy')->name('destroy');
    });
});

Route::group(['middleware' => 'auth'], function () {
    // Generate PDF
    Route::get('/purchase-returns/pdf/{id}', function ($id) {
        $purchaseReturn = PurchaseReturn::findOrFail($id);
        $supplier = Supplier::findOrFail($purchaseReturn->supplier_id);
        $pdf = PDF::loadView('purchasesreturn.print', [
            'purchase_return' => $purchaseReturn,
            'supplier' => $supplier,
        ])->setPaper('a4');

        return $pdf->stream('purchase-return-'.$purchaseReturn->reference.'.pdf');
    })->name('purchase-returns.pdf');
    // Purchase Returns
    Route::resource('purchase-returns', 'PurchasesReturnController');
    // Payments
    Route::group(['prefix' => 'purchase-return-payments', 'as' => 'purchase-return-payments.'], function () {
        Route::get('/{purchase_return_id}', 'PurchaseReturnPaymentsController@index')->name('index');
        Route::get('/{purchase_return_id}/create', 'PurchaseReturnPaymentsController@create')->name('create');
        Route::post('/store', 'PurchaseReturnPaymentsController@store')->name('store');
        Route::get('/{purchase_return_id}/edit/{purchaseReturnPayment}', 'PurchaseReturnPaymentsController@edit')->name('edit');
        Route::patch('/update/{purchaseReturnPayment}', 'PurchaseReturnPaymentsController@update')->name('update');
        Route::delete('/destroy/{purchaseReturnPayment}', 'PurchaseReturnPaymentsController@destroy')->name('destroy');
    });
});

Route::group(['middleware' => 'auth'], function () {
    // POS
    Route::get('/app/pos', 'PosController@index')->name('app.pos.index');
    Route::post('/app/pos', 'PosController@store')->name('app.pos.store');
    // Generate PDF
    Route::get('/sales/pdf/{id}', function ($id) {
        $sale = Sale::findOrFail($id);
        $customer = Customer::findOrFail($sale->customer_id);
        $pdf = PDF::loadView('sale.print', [
            'sale' => $sale,
            'customer' => $customer,
        ])->setPaper('a4');

        return $pdf->stream('sale-'.$sale->reference.'.pdf');
    })->name('sales.pdf');
    Route::get('/sales/pos/pdf/{id}', function ($id) {
        $sale = Sale::findOrFail($id);
        $pdf = PDF::loadView('sale.print-pos', [
            'sale' => $sale,
        ])->setPaper('a7');

        return $pdf->stream('sale-'.$sale->reference.'.pdf');
    })->name('sales.pos.pdf');
    // Sales
    Route::resource('sales', 'SaleController');
    // Payments
    Route::group(['prefix' => 'sale-payments', 'as' => 'sale-payments.'], function () {
        Route::get('/{sale_id}', 'SalePaymentsController@index')->name('index');
        Route::get('/{sale_id}/create', 'SalePaymentsController@create')->name('create');
        Route::post('/store', 'SalePaymentsController@store')->name('store');
        Route::get('/{sale_id}/edit/{salePayment}', 'SalePaymentsController@edit')->name('edit');
        Route::patch('/update/{salePayment}', 'SalePaymentsController@update')->name('update');
        Route::delete('/destroy/{salePayment}', 'SalePaymentsController@destroy')->name('destroy');
    });
});

Route::group(['middleware' => 'auth'], function () {
    // Generate PDF
    Route::get('/sale-returns/pdf/{id}', function ($id) {
        $saleReturn = SaleReturn::findOrFail($id);
        $customer = Customer::findOrFail($saleReturn->customer_id);
        $pdf = PDF::loadView('salesreturn.print', [
            'sale_return' => $saleReturn,
            'customer' => $customer,
        ])->setPaper('a4');

        return $pdf->stream('sale-return-'.$saleReturn->reference.'.pdf');
    })->name('sale-returns.pdf');
    // Sale Returns
    Route::resource('sale-returns', 'SalesReturnController');
    // Payments
    Route::group(['prefix' => 'sale-return-payments', 'as' => 'sale-return-payments.'], function () {
        Route::get('/{sale_return_id}', 'SaleReturnPaymentsController@index')->name('index');
        Route::get('/{sale_return_id}/create', 'SaleReturnPaymentsController@create')->name('create');
        Route::post('/store', 'SaleReturnPaymentsController@store')->name('store');
        Route::get('/{sale_return_id}/edit/{saleReturnPayment}', 'SaleReturnPaymentsController@edit')->name('edit');
        Route::patch('/update/{saleReturnPayment}', 'SaleReturnPaymentsController@update')->name('update');
        Route::delete('/destroy/{saleReturnPayment}', 'SaleReturnPaymentsController@destroy')->name('destroy');
    });
});

Route::group(['middleware' => 'auth'], function () {
    // Generate PDF
    Route::get('/quotations/pdf/{id}', function ($id) {
        $quotation = Quotation::findOrFail($id);
        $customer = Customer::findOrFail($quotation->customer_id);
        $pdf = PDF::loadView('quotation.print', [
            'quotation' => $quotation,
            'customer' => $customer,
        ])->setPaper('a4');

        return $pdf->stream('quotation-'.$quotation->reference.'.pdf');
    })->name('quotations.pdf');
    // Send Quotation Mail
    Route::get('/quotation/mail/{quotation}', 'SendQuotationEmailController')->name('quotation.email');
    // Sales Form Quotation
    Route::get('/quotation-sales/{quotation}', 'QuotationSalesController')->name('quotation-sales.create');
    // quotations
    Route::resource('quotations', 'QuotationController');
});

Route::group(['middleware' => 'auth'], function () {
    // Devis → Bon de Commande → Commande → Facture workflow.

    // Devis → Bon de Commande
    Route::post('/quotations/{quotation}/convert-to-bon-commande', 'Convert\QuotationToBonCommandeController')
        ->name('quotations.convert');

    // Bon de Commande
    Route::resource('bon-commandes', 'BonCommandeController')
        ->parameters(['bon-commandes' => 'bonCommande'])
        ->only(['index', 'show', 'edit', 'update', 'destroy']);
    Route::post('/bon-commandes/{bonCommande}/confirm', 'BonCommandeController@confirm')->name('bon-commandes.confirm');
    Route::post('/bon-commandes/{bonCommande}/cancel', 'BonCommandeController@cancel')->name('bon-commandes.cancel');

    // Bon de Commande → Commande
    Route::post('/bon-commandes/{bonCommande}/convert-to-commande', 'Convert\BonCommandeToCommandeController')
        ->name('bon-commandes.convert');

    // Commande
    Route::resource('commandes', 'CommandeController')
        ->parameters(['commandes' => 'commande'])
        ->only(['index', 'show', 'destroy']);
    Route::post('/commandes/{commande}/confirm', 'CommandeController@confirm')->name('commandes.confirm');

    // Commande → Facture (Sale)
    Route::post('/commandes/{commande}/generate-facture', 'Convert\CommandeToFactureController')
        ->name('commandes.convert');
});

Route::group(['middleware' => 'auth'], function () {
    // User Profile
    Route::get('/user/profile', 'ProfileController@edit')->name('profile.edit');
    Route::patch('/user/profile', 'ProfileController@update')->name('profile.update');
    Route::patch('/user/password', 'ProfileController@updatePassword')->name('profile.update.password');
    // Users
    Route::resource('users', 'UsersController')->except('show');
    // Roles
    Route::resource('roles', 'RolesController')->except('show');
});

Route::group(['middleware' => 'auth'], function () {
    // Activity Logs
    Route::get('/activity-logs', 'ActivityController@index')->name('activity-logs.index');
    Route::delete('/activity-logs/clear', 'ActivityController@clear')->name('activity-logs.clear');
    Route::get('/activity-logs/{activity}', 'ActivityController@show')->name('activity-logs.show');
    Route::delete('/activity-logs/{activity}', 'ActivityController@destroy')->name('activity-logs.destroy');
});

Route::group(['middleware' => 'auth'], function () {
    // Profit Loss Report
    Route::get('/profit-loss-report', 'ReportsController@profitLossReport')
        ->name('profit-loss-report.index');
    // Payments Report
    Route::get('/payments-report', 'ReportsController@paymentsReport')
        ->name('payments-report.index');
    // Sales Report
    Route::get('/sales-report', 'ReportsController@salesReport')
        ->name('sales-report.index');
    // Purchases Report
    Route::get('/purchases-report', 'ReportsController@purchasesReport')
        ->name('purchases-report.index');
    // Sales Return Report
    Route::get('/sales-return-report', 'ReportsController@salesReturnReport')
        ->name('sales-return-report.index');
    // Purchases Return Report
    Route::get('/purchases-return-report', 'ReportsController@purchasesReturnReport')
        ->name('purchases-return-report.index');
    // Inventory Valuation Report
    Route::get('/inventory-valuation-report', 'ReportsController@inventoryValuationReport')
        ->name('inventory-valuation-report.index');
    // Low Stock Report
    Route::get('/low-stock-report', 'ReportsController@lowStockReport')
        ->name('low-stock-report.index');
    // Stock Movement Report
    Route::get('/stock-movement-report', 'ReportsController@stockMovementReport')
        ->name('stock-movement-report.index');
    // Fast / Slow Moving Products Report
    Route::get('/product-movement-report', 'ReportsController@productMovementReport')
        ->name('product-movement-report.index');
});

Route::group(['middleware' => 'auth'], function () {
    // Dropzone
    Route::post('/dropzone/upload', 'UploadController@dropzoneUpload')->name('dropzone.upload');
    Route::post('/dropzone/delete', 'UploadController@dropzoneDelete')->name('dropzone.delete');
    // Filepond
    Route::post('/filepond/upload', 'UploadController@filepondUpload')->name('filepond.upload');
    Route::delete('/filepond/delete', 'UploadController@filepondDelete')->name('filepond.delete');
});
