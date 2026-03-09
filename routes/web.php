<?php

use App\Http\Controllers\Admin\BurgerController as AdminBurgerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderManagementController;
use App\Http\Controllers\Client\CatalogController;
use App\Http\Controllers\Client\OrderController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/catalogue');

Route::prefix('catalogue')->name('catalog.')->group(function (): void {
    Route::get('/', [CatalogController::class, 'index'])->name('index');
    Route::get('/burger/{burger}', [CatalogController::class, 'show'])->name('show');
});

Route::post('/commandes', [OrderController::class, 'store'])->name('orders.store');
Route::get('/commandes/{order}/confirmation', [OrderController::class, 'confirmation'])->name('orders.confirmation');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('burgers', AdminBurgerController::class)->except('show');
    Route::patch('burgers/{burger}/restore', [AdminBurgerController::class, 'restore'])->name('burgers.restore');

    Route::get('orders', [OrderManagementController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderManagementController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('orders/{order}/cancel', [OrderManagementController::class, 'cancel'])->name('orders.cancel');
    Route::post('orders/{order}/pay', [OrderManagementController::class, 'pay'])->name('orders.pay');
    Route::get('orders/{order}/invoice', [OrderManagementController::class, 'invoice'])->name('orders.invoice');
});
