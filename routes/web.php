<?php

use App\Http\Controllers\Admin\AvailableDateController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/catalog', [PageController::class, 'catalog'])->name('catalog');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:customer'])->group(function (): void {
    Route::get('/order', [CustomerOrderController::class, 'create'])->name('order');
    Route::post('/order', [CustomerOrderController::class, 'store'])->name('order.store');
    Route::put('/orders/{order}', [CustomerOrderController::class, 'update'])->name('orders.update');
    Route::patch('/orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/history', [CustomerOrderController::class, 'history'])->name('history');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', fn () => redirect()->route('admin.orders.index'))->name('dashboard');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');

    Route::get('/dates', [AvailableDateController::class, 'index'])->name('dates.index');
    Route::post('/dates', [AvailableDateController::class, 'store'])->name('dates.store');
    Route::post('/dates/toggle', [AvailableDateController::class, 'toggle'])->name('dates.toggle');
    Route::put('/dates/{availableDate}', [AvailableDateController::class, 'update'])->name('dates.update');
    Route::delete('/dates/{availableDate}', [AvailableDateController::class, 'destroy'])->name('dates.destroy');

    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::put('/menu/{menuItem}', [MenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{menuItem}', [MenuController::class, 'destroy'])->name('menu.destroy');
});
