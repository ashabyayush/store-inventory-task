<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::view('/orders/new', 'pages.new-order')->name('orders.create');
    Route::view('/history', 'pages.history')->name('orders.history');
    Route::view('/customers', 'pages.customers')->name('customers.index');
    Route::view('/products', 'pages.products')->name('products.index');
    Route::view('/categories', 'pages.categories')->name('categories.index');
    Route::view('/low-stock', 'pages.low-stock')->name('products.low');
    Route::view('/settings', 'pages.settings')->name('settings.index');
});