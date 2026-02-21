<?php

use App\Http\Controllers\{
    DashboardController,
    ProductController,
    ClientController,
    InvoiceController,
    QuickSaleController,
    PurchaseController,
    ExpenseController,
    ReportExportController,
    CategoryController,
    SupplierController,
    UserController,
};
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Redirect / to login or dashboard
Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

// Include authentication routes
require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', ProductController::class);
    Route::get('/products/{product}/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
    Route::patch('/products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('products.adjust-stock');
    Route::get('/api/products/subcategories/{mainCategoryId}', [ProductController::class, 'getSubcategories'])->name('products.getSubcategories');
    Route::get('/api/products/sku/{productName}', [ProductController::class, 'generateSku'])->name('products.generateSku');

    // Clients
    Route::resource('clients', ClientController::class);
    Route::get('/clients/{client}/invoices', [ClientController::class, 'invoices'])->name('clients.invoices');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::post('/invoices/{invoice}/record-payment', [InvoiceController::class, 'recordPayment'])->name('invoices.record-payment');

    // Quick Sales
    Route::resource('quick-sales', QuickSaleController::class);

    // Purchases
    Route::resource('purchases', PurchaseController::class);
    Route::post('/purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');

    // Expenses
    Route::resource('expenses', ExpenseController::class);

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [DashboardController::class, 'salesReport'])->name('sales');
        Route::get('/profit', [DashboardController::class, 'profitReport'])->name('profit');
        Route::get('/inventory', [DashboardController::class, 'inventoryReport'])->name('inventory');
        Route::get('/daily-closing', [DashboardController::class, 'dailyClosingReport'])->name('daily-closing');

        // Export Routes
        Route::post('/export-pdf/{type}', [ReportExportController::class, 'exportPdf'])->name('export-pdf');
        Route::post('/export-excel/{type}', [ReportExportController::class, 'exportExcel'])->name('export-excel');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::resource('categories', CategoryController::class)->except('show');

        // Sub-categories routes
        Route::get('categories/{category}/subcategories', [CategoryController::class, 'showSubcategories'])->name('categories.subcategories');
        Route::post('categories/{category}/subcategories', [CategoryController::class, 'storeSubcategory'])->name('categories.storeSubcategory');
        Route::put('categories/{category}/subcategories/{subcategory}', [CategoryController::class, 'updateSubcategory'])->name('categories.updateSubcategory');
        Route::delete('categories/{category}/subcategories/{subcategory}', [CategoryController::class, 'destroySubcategory'])->name('categories.destroySubcategory');

        Route::resource('suppliers', SupplierController::class)->except('show');
        Route::resource('users', UserController::class)->except('show');
    });
});
