<?php

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
    if (in_array($locale, \App\Http\Middleware\SetLocale::SUPPORTED_LOCALES, true)) {
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
    Route::get('/purchase-payments/{purchase_id}', 'PurchasePaymentsController@index')->name('purchase-payments.index');
    Route::get('/purchase-payments/{purchase_id}/create', 'PurchasePaymentsController@create')->name('purchase-payments.create');
    Route::post('/purchase-payments/store', 'PurchasePaymentsController@store')->name('purchase-payments.store');
    Route::get('/purchase-payments/{purchase_id}/edit/{purchasePayment}', 'PurchasePaymentsController@edit')->name('purchase-payments.edit');
    Route::patch('/purchase-payments/update/{purchasePayment}', 'PurchasePaymentsController@update')->name('purchase-payments.update');
    Route::delete('/purchase-payments/destroy/{purchasePayment}', 'PurchasePaymentsController@destroy')->name('purchase-payments.destroy');
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
    Route::get('/purchase-return-payments/{purchase_return_id}', 'PurchaseReturnPaymentsController@index')
        ->name('purchase-return-payments.index');
    Route::get('/purchase-return-payments/{purchase_return_id}/create', 'PurchaseReturnPaymentsController@create')
        ->name('purchase-return-payments.create');
    Route::post('/purchase-return-payments/store', 'PurchaseReturnPaymentsController@store')
        ->name('purchase-return-payments.store');
    Route::get('/purchase-return-payments/{purchase_return_id}/edit/{purchaseReturnPayment}', 'PurchaseReturnPaymentsController@edit')
        ->name('purchase-return-payments.edit');
    Route::patch('/purchase-return-payments/update/{purchaseReturnPayment}', 'PurchaseReturnPaymentsController@update')
        ->name('purchase-return-payments.update');
    Route::delete('/purchase-return-payments/destroy/{purchaseReturnPayment}', 'PurchaseReturnPaymentsController@destroy')
        ->name('purchase-return-payments.destroy');
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
    Route::get('/sale-payments/{sale_id}', 'SalePaymentsController@index')->name('sale-payments.index');
    Route::get('/sale-payments/{sale_id}/create', 'SalePaymentsController@create')->name('sale-payments.create');
    Route::post('/sale-payments/store', 'SalePaymentsController@store')->name('sale-payments.store');
    Route::get('/sale-payments/{sale_id}/edit/{salePayment}', 'SalePaymentsController@edit')->name('sale-payments.edit');
    Route::patch('/sale-payments/update/{salePayment}', 'SalePaymentsController@update')->name('sale-payments.update');
    Route::delete('/sale-payments/destroy/{salePayment}', 'SalePaymentsController@destroy')->name('sale-payments.destroy');
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
    Route::get('/sale-return-payments/{sale_return_id}', 'SaleReturnPaymentsController@index')
        ->name('sale-return-payments.index');
    Route::get('/sale-return-payments/{sale_return_id}/create', 'SaleReturnPaymentsController@create')
        ->name('sale-return-payments.create');
    Route::post('/sale-return-payments/store', 'SaleReturnPaymentsController@store')
        ->name('sale-return-payments.store');
    Route::get('/sale-return-payments/{sale_return_id}/edit/{saleReturnPayment}', 'SaleReturnPaymentsController@edit')
        ->name('sale-return-payments.edit');
    Route::patch('/sale-return-payments/update/{saleReturnPayment}', 'SaleReturnPaymentsController@update')
        ->name('sale-return-payments.update');
    Route::delete('/sale-return-payments/destroy/{saleReturnPayment}', 'SaleReturnPaymentsController@destroy')
        ->name('sale-return-payments.destroy');
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

