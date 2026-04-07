<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LocaleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderItemController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::post('locale', [LocaleController::class, 'update'])->name('locale');
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login']);

        Route::middleware(['auth', 'admin'])->group(function (): void {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            Route::resource('users', UserController::class)->only(['index', 'edit', 'update', 'destroy']);
            Route::resource('products', ProductController::class)->except(['show']);
            Route::resource('orders', OrderController::class)->only(['index', 'show', 'edit', 'update']);
            Route::get('order-items/{orderItem}/edit', [OrderItemController::class, 'edit'])->name('order-items.edit');
            Route::put('order-items/{orderItem}', [OrderItemController::class, 'update'])->name('order-items.update');
            Route::delete('order-items/{orderItem}', [OrderItemController::class, 'destroy'])->name('order-items.destroy');
        });
    });
