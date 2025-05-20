<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProductSettingsController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\TaxController;

Route::get('/', function () {
    return view('welcome');
});

// Language switching route
Route::get('locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // مسارات إعدادات المنتجات
    Route::prefix('settings')->name('settings.')->group(function () {
        // إعدادات المنتجات العامة
        Route::get('/products', [ProductSettingsController::class, 'index'])->name('products.index');
        Route::post('/products/initialize-defaults', [ProductSettingsController::class, 'initializeDefaults'])->name('products.initialize-defaults');
        
        // وحدات القياس
        Route::prefix('units')->name('units.')->group(function () {
            Route::get('/', [UnitController::class, 'index'])->name('index');
            Route::get('/create', [UnitController::class, 'create'])->name('create');
            Route::post('/', [UnitController::class, 'store'])->name('store');
            Route::get('/{unit}/edit', [UnitController::class, 'edit'])->name('edit');
            Route::put('/{unit}', [UnitController::class, 'update'])->name('update');
            Route::delete('/{unit}', [UnitController::class, 'destroy'])->name('destroy');
            Route::patch('/{unit}/toggle-status', [UnitController::class, 'toggleStatus'])->name('toggle-status');
        });
        
        // الضرائب
        Route::prefix('taxes')->name('taxes.')->group(function () {
            Route::get('/', [TaxController::class, 'index'])->name('index');
            Route::get('/create', [TaxController::class, 'create'])->name('create');
            Route::post('/', [TaxController::class, 'store'])->name('store');
            Route::get('/{tax}/edit', [TaxController::class, 'edit'])->name('edit');
            Route::put('/{tax}', [TaxController::class, 'update'])->name('update');
            Route::delete('/{tax}', [TaxController::class, 'destroy'])->name('destroy');
            Route::patch('/{tax}/toggle-status', [TaxController::class, 'toggleStatus'])->name('toggle-status');
        });
    });
    
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
