<?php

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::prefix('cart/{cartId}')->group(function () {
    Route::post('/add-cart-item', [CartController::class, 'addCartItem'])->name('api.cart.addCartItem');
    Route::put('/update-cart', [CartController::class, 'updateCart'])->name('api.cart.updateCart');
    Route::delete('/remove-cart-item/{cartItemId}', [CartController::class, 'removeCartItem'])->name('api.cart.removeCartItem');
    Route::get('/cart-items/count', [CartController::class, 'getTotalProducts'])->name('api.cart.getTotalProducts');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('api.cart.checkout');
});