<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ProductSettingsController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\TaxController;

Route::get('/', function () {
    return view('welcome');
});

// Language switching route
Route::get('locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Auth::routes();

// Temporary route to check database tables
Route::get('/check-tables', function() {
    try {
        $tables = DB::select('SHOW TABLES');
        $db = 'Tables_in_' . DB::connection()->getDatabaseName();
        return array_map(function($table) use ($db) {
            return $table->$db;
        }, $tables);
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
})->middleware('auth');

// Test route for currency symbol
Route::get('/test-currency-symbol', function() {
    if (function_exists('currency_symbol')) {
        return 'Currency symbol function exists. Symbol: ' . currency_symbol();
    } else {
        return 'Currency symbol function does not exist';
    }
});


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

    // مسارات تعديل المنتج منفصلة
    Route::put('/products/{product}/update-basic', [ProductController::class, 'updateBasic'])->name('products.update-basic');
    Route::put('/products/{product}/update-pricing', [ProductController::class, 'updatePricing'])->name('products.update-pricing');
    Route::put('/products/{product}/update-details', [ProductController::class, 'updateDetails'])->name('products.update-details');
    Route::put('/products/{product}/update-main-image', [ProductController::class, 'updateMainImage'])->name('products.update-main-image');
    Route::put('/products/{product}/update-gallery', [ProductController::class, 'updateGallery'])->name('products.update-gallery');
    Route::put('/products/{product}/update-barcode', [ProductController::class, 'updateBarcode'])->name('products.update-barcode');
    Route::put('/products/{product}/update-extra-image', [ProductController::class, 'updateExtraImage'])->name('products.update-extra-image');

    // مسار حذف صورة من المعرض
    Route::delete('/product-images/{image}', [ProductImageController::class, 'destroy'])->name('product-images.destroy');
    Route::put('/product-images/{image}', [ProductImageController::class, 'update'])->name('product-images.update');
    Route::post('/product-images/{image}/replace', [ProductImageController::class, 'replace'])->name('product-images.replace');
        
    // مسارات إضافية للمنتجات
    Route::prefix('products')->group(function () {
        // تغيير حالة المنتج (نشط/غير نشط)
        Route::patch('{product}/toggle-status', [ProductController::class, 'toggleStatus'])
            ->name('products.toggle-status');
            
        // استيراد وتصدير المنتجات
        Route::get('import', [ProductController::class, 'showImportForm'])->name('products.import.form');
        Route::post('import', [ProductController::class, 'import'])->name('products.import');
        Route::get('export', [ProductController::class, 'export'])->name('products.export');
        
        // مسارات الباركود
        Route::get('{product}/print-barcode', [ProductController::class, 'printBarcode'])
            ->name('products.print-barcode');
            
        // مسارات العروض
        Route::get('{product}/offers/create', [ProductController::class, 'createOffer'])->name('products.offers.create');
        Route::post('{product}/offers', [ProductController::class, 'storeOffer'])->name('products.offers.store');
            
        // تحديث حقل معين للمنتج (AJAX)
        Route::patch('{product}/update-field', [ProductController::class, 'updateField'])->name('products.update-field');
            
        // توليد الأكواد تلقائيًا
        Route::get('generate-barcode', [ProductController::class, 'generateBarcode'])->name('products.generate-barcode');
            
        // توليد كود منتج جديد
        Route::get('generate-product-code', [ProductController::class, 'generateProductCode'])->name('products.generate-product-code');
            
        // توليد SKU جديد
        Route::get('generate-sku', [ProductController::class, 'generateSKU'])->name('products.generate-sku');
        Route::get('discount', [ProductController::class, 'discount'])->name('products.discount');
    });
    
    // مسارات الفئات
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/search', [CategoryController::class, 'index'])->name('search'); // مسار البحث عبر AJAX
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // مسارات العملاء
    Route::resource('customers', CustomerController::class);

    // مسارات نقطة البيع
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/products', [PosController::class, 'getProducts'])->name('pos.products');
    Route::get('/pos/customers', [PosController::class, 'getCustomers'])->name('pos.customers');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    // مسارات طلبات الشراء
    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::post('/', [ProductController::class, 'storePurchaseOrder'])->name('store');
        Route::get('/', [ProductController::class, 'indexPurchaseOrders'])->name('index');
        Route::get('/{order}', [ProductController::class, 'showPurchaseOrder'])->name('show');
        Route::get('/{order}/edit', [ProductController::class, 'editPurchaseOrder'])->name('edit');
        Route::put('/{order}', [ProductController::class, 'updatePurchaseOrder'])->name('update');
        Route::delete('/{order}', [ProductController::class, 'destroyPurchaseOrder'])->name('destroy');
    });

    // مسارات الفواتير
    Route::resource('invoices', InvoiceController::class);
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::get('/invoices/{invoice}/print-direct', [InvoiceController::class, 'printDirect'])->name('invoices.print-direct');
    Route::post('/invoices/{invoice}/payment', [InvoiceController::class, 'addPayment'])->name('invoices.payment');
});
