<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\CustomerController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::middleware('auth:api')->prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('customers', [CustomerController::class, 'store'])->name('customer.create');
        Route::get('customers', [CustomerController::class, 'index'])->name('customer.list');
    });
});
