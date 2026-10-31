<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerOrderController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::get('/products/low-stock', [ProductController::class, 'lowStock']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);

    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/orders', [CustomerOrderController::class, 'index']);

    Route::get('/settings', [SettingController::class, 'show']);
    Route::put('/settings/currency', [SettingController::class, 'updateCurrency']);
    Route::put('/settings/account', [SettingController::class, 'updateAccount']);
});