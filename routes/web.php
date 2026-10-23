<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

// ---- Guest ----
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
});

// ---- Terautentikasi ----
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin saja: CRUD master data (create/edit/delete produk, kategori, pemasok) & pengguna.
    Route::middleware('can:manage-master')->group(function () {
        Route::resource('products', ProductController::class)->except(['index', 'show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('suppliers', SupplierController::class)->except(['show']);
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Semua user login boleh MELIHAT produk.
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
    Route::get('/products/{product}', [ProductController::class, 'show'])->whereNumber('product')->name('products.show');

    // Transaksi: Admin & Staff. Pembatalan: Admin saja.
    Route::middleware('can:manage-transactions')->group(function () {
        Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])
            ->middleware('can:cancel-transaction')->name('purchases.cancel');

        Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])
            ->middleware('can:cancel-transaction')->name('sales.cancel');
    });
});
