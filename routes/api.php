<?php

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::prefix('cart/{cartId}')->group(function (): void {
    Route::post('/products', [CartController::class, 'addProduct']);
    Route::put('/products/{productId}', [CartController::class, 'updateProduct']);
    Route::delete('/products/{productId}', [CartController::class, 'removeProduct']);
    Route::get('/products/count', [CartController::class, 'getTotalProducts']);
    Route::post('/checkout', [CartController::class, 'checkout']);
});
