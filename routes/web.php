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

Route::get('/', function () {
    return view('welcome');
});

// Language switching route
Route::get('locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // مسارات المنتجات
    Route::resource('products', ProductController::class);
    
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
    Route::post('/invoices/{invoice}/payment', [InvoiceController::class, 'addPayment'])->name('invoices.payment');
});
