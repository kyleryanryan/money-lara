<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MoneyStatisticsController;

Route::group(['middleware' => 'api'], function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/products/{id}/convert', [ProductController::class, 'convertPrice']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::post('/cart/apply-flat-discount', [CartController::class, 'applyFlatDiscount']);
    Route::post('/cart/apply-percent-discount', [CartController::class, 'applyPercentDiscount']);
    Route::post('/cart/add-with-quantity', [CartController::class, 'addToCartWithQuantity']);
    Route::post('/cart/installments', [CartController::class, 'calculateInstallments']);
    Route::post('/money/statistics', [MoneyStatisticsController::class, 'calculateStatistics']);
});