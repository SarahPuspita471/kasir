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
});

/*
|--------------------------------------------------------------------------
| User area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserDashboard::class, 'index'])->name('user.dashboard');

    Route::prefix('user')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('user.pos.index');
        Route::get('/history', [HistoryController::class, 'index'])->name('user.history.index');
        Route::get('/stock', [StockController::class, 'index'])->name('user.stock.index');
        Route::get('/customers', [CustomersController::class, 'index'])->name('user.customers.index');
        Route::get('/reports', [ReportsController::class, 'index'])->name('user.reports.index');
    });
});