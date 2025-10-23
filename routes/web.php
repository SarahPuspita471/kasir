<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\User\PosController;
use App\Http\Controllers\User\HistoryController;
use App\Http\Controllers\User\StockController;
use App\Http\Controllers\User\CustomersController;
use App\Http\Controllers\User\ReportsController;
use App\Http\Controllers\Admin\StockTransferController;
use App\Http\Controllers\Admin\ProductPriceController;
use App\Http\Controllers\Admin\ProductBarcodeController;
use App\Http\Controllers\User\DiscountApiController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\DiscountSchemeController;
use App\Http\Controllers\Admin\DiscountTierController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\User\TaxInfoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'show'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.attempt')->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Admin area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminDashboard::class, 'index'])->name('admin.dashboard');
    Route::resource('/admin/users', \App\Http\Controllers\Admin\UserController::class)
        ->names('admin.users')
        ->except(['show']);

    Route::post('/admin/users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])
        ->name('admin.users.reset');

    Route::get('/admin/stock', [StockTransferController::class, 'index'])->name('admin.stock.index');
    Route::post('/admin/stock/ambil', [StockTransferController::class, 'ambil'])->name('admin.stock.ambil');
    Route::post('/admin/products/set-price', [ProductPriceController::class, 'update'])
        ->name('admin.products.set-price');

    Route::get('/admin/products/{product}/barcode/preview', [ProductBarcodeController::class, 'preview'])
        ->name('admin.products.barcode.preview');
    Route::get('/admin/products/{product}/barcode/download', [ProductBarcodeController::class, 'download'])
        ->name('admin.products.barcode.download');

    // VOUCHERS
    Route::get('/admin/vouchers', [VoucherController::class, 'index'])->name('admin.vouchers.index');
    Route::get('/admin/vouchers/create', [VoucherController::class, 'create'])->name('admin.vouchers.create');
    Route::post('/admin/vouchers', [VoucherController::class, 'store'])->name('admin.vouchers.store');
    Route::get('/admin/vouchers/{voucher}/edit', [VoucherController::class, 'edit'])->name('admin.vouchers.edit');
    Route::put('/admin/vouchers/{voucher}', [VoucherController::class, 'update'])->name('admin.vouchers.update');
    Route::delete('/admin/vouchers/{voucher}', [VoucherController::class, 'destroy'])->name('admin.vouchers.destroy');
    Route::patch('/admin/vouchers/{voucher}/toggle', [VoucherController::class, 'toggle'])->name('admin.vouchers.toggle');

    // DISCOUNT SCHEMES
    Route::get('/admin/discounts', [DiscountSchemeController::class, 'index'])->name('admin.discounts.index');
    Route::get('/admin/discounts/create', [DiscountSchemeController::class, 'create'])->name('admin.discounts.create');
    Route::post('/admin/discounts', [DiscountSchemeController::class, 'store'])->name('admin.discounts.store');
    Route::get('/admin/discounts/{scheme}/edit', [DiscountSchemeController::class, 'edit'])->name('admin.discounts.edit');
    Route::put('/admin/discounts/{scheme}', [DiscountSchemeController::class, 'update'])->name('admin.discounts.update');
    Route::delete('/admin/discounts/{scheme}', [DiscountSchemeController::class, 'destroy'])->name('admin.discounts.destroy');

    // TIERS (inline manage di halaman scheme)
    Route::post('/admin/discounts/{scheme}/tiers', [DiscountTierController::class, 'store'])->name('admin.tiers.store');
    Route::put('/admin/tiers/{tier}', [DiscountTierController::class, 'update'])->name('admin.tiers.update');
    Route::delete('/admin/tiers/{tier}', [DiscountTierController::class, 'destroy'])->name('admin.tiers.destroy');


});

// routes/web.php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/transactions', [\App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{sale}', [\App\Http\Controllers\Admin\TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/tax', [TaxController::class, 'edit'])->name('tax.edit');
    Route::post('/tax', [TaxController::class, 'update'])->name('tax.update');
});


/*
|--------------------------------------------------------------------------
| User area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserDashboard::class, 'index'])->name('user.dashboard');
    Route::get('/user/pos', [PosController::class, 'index'])->name('user.pos.index');
    Route::get('/user/pos/products', [PosController::class, 'products'])->name('user.pos.products'); // JSON

    Route::prefix('user')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('user.pos.index');
        Route::get('/history', [HistoryController::class, 'index'])->name('user.history.index');
        Route::get('/stock', [StockController::class, 'index'])->name('user.stock.index');
        Route::get('/customers', [CustomersController::class, 'index'])->name('user.customers.index');
        Route::get('/reports', [ReportsController::class, 'index'])->name('user.reports.index');
    });

    Route::post('/user/pos/discount/preview', [DiscountApiController::class, 'previewAuto'])
        ->name('user.pos.discount.preview');

    // Validasi voucher + hold redemption
    Route::post('/user/pos/voucher/validate', [DiscountApiController::class, 'validateVoucher'])
        ->name('user.pos.voucher.validate');

    // Batalkan hold (mis. user ganti voucher / batal transaksi)
    Route::post('/user/pos/voucher/void', [DiscountApiController::class, 'voidHeld'])
        ->name('user.pos.voucher.void');

    // POS data
    Route::get('/user/pos/products', [\App\Http\Controllers\User\PosController::class, 'products'])
        ->name('user.pos.products');

    // Discount API (TAHAP 2, sudah ada)
    Route::post('/user/pos/discount/preview', [DiscountApiController::class, 'previewAuto'])
        ->name('user.pos.discount.preview');
    Route::post('/user/pos/voucher/validate', [DiscountApiController::class, 'validateVoucher'])
        ->name('user.pos.voucher.validate');
    Route::post('/user/pos/voucher/void', [DiscountApiController::class, 'voidHeld'])
        ->name('user.pos.voucher.void');

    // CHECKOUT + STRUK
    Route::post('/user/pos/checkout', [CheckoutController::class, 'store'])
        ->name('user.pos.checkout');
    Route::get('/user/sales/{sale}/receipt', [CheckoutController::class, 'receipt'])
        ->name('user.sales.receipt');


});

Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{sale}', [HistoryController::class, 'show'])
        ->whereNumber('sale')
        ->name('history.show');
    Route::get('/pos/tax', [TaxInfoController::class, 'show'])->name('pos.tax.show');
});
