<?php

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::prefix('cart/{cartId}')->group(function () {
    Route::post('/products', [CartController::class, 'addProduct'])->name('api.cart.addProduct');
    Route::put('/update-products', [CartController::class, 'updateProduct'])->name('api.cart.updateProduct');
    Route::delete('/products/{productId}', [CartController::class, 'removeProduct'])->name('api.cart.removeProduct');
    Route::get('/products/count', [CartController::class, 'getTotalProducts'])->name('api.cart.getTotalProducts');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('api.cart.checkout');
});