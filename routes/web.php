<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // مسارات المنتجات
    Route::resource('products', ProductController::class);
    
    // مسارات إضافية للمنتجات
    Route::prefix('products')->group(function () {
        // تغيير حالة المنتج (نشط/غير نشط)
        Route::patch('{product}/toggle-status', [ProductController::class, 'toggleStatus'])
            ->name('products.toggle-status');
            
        // استيراد وتصدير المنتجات
        Route::get('import', [ProductController::class, 'showImportForm'])->name('products.import.form');
        Route::post('import', [ProductController::class, 'import'])->name('products.import');
        Route::get('export', [ProductController::class, 'export'])->name('products.export');
        
        // تحديث حقل معين للمنتج (AJAX)
        Route::patch('{product}/update-field', [ProductController::class, 'updateField'])
            ->name('products.update-field');
            
        // توليد باركود جديد
        Route::get('generate-barcode', [ProductController::class, 'generateBarcode'])
            ->name('products.generate-barcode');
    });
    
    // مسارات الفئات
    Route::resource('categories', CategoryController::class);

    // مسارات العملاء
    Route::resource('customers', CustomerController::class);

    // مسارات نقطة البيع
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/products', [PosController::class, 'getProducts'])->name('pos.products');
    Route::get('/pos/customers', [PosController::class, 'getCustomers'])->name('pos.customers');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    // مسارات الفواتير
    Route::resource('invoices', InvoiceController::class);
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
Route::get('/invoices/{invoice}/print-direct', [InvoiceController::class, 'printDirect'])->name('invoices.print-direct');
    Route::post('/invoices/{invoice}/payment', [InvoiceController::class, 'addPayment'])->name('invoices.payment');
});
