<?php

use App\Http\Middleware\SetLocale;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Quotation;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Supplier;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    }

    return view('welcome');
})->name('welcome');

Route::get('/language/{locale}', function (string $locale) {
    if (in_array($locale, SetLocale::SUPPORTED_LOCALES, true)) {
        session()->put('locale', $locale);
    }

    return redirect()->back();
})->name('language.switch');

Auth::routes(['register' => false]);

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

Route::group(['middleware' => 'auth'], function () {
    // Customers
    Route::resource('customers', 'CustomersController');
    // Suppliers
    Route::resource('suppliers', 'SuppliersController');
});

Route::group(['middleware' => 'auth'], function () {
    Route::resource('currencies', 'CurrencyController')->except('show');
});

Route::group(['middleware' => 'auth'], function () {
    // Mail Settings
    Route::patch('/settings/smtp', 'SettingController@updateSmtp')->name('settings.smtp.update');
    // General Settings
    Route::get('/settings', 'SettingController@index')->name('settings.index');
    Route::patch('/settings', 'SettingController@update')->name('settings.update');
    // Units
    Route::resource('units', 'UnitsController')->except('show');
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
        ])->setPaper('a7')
            ->setOption('margin-top', 8)
            ->setOption('margin-bottom', 8)
            ->setOption('margin-left', 5)
            ->setOption('margin-right', 5);

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
