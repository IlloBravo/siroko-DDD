<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect('/all-products-available');
});

Route::get('/all-products-available', [ProductController::class, 'index'])->name('products.index');

Route::prefix('/{cartId}')->group(function () {
    Route::get('/cart/view', [CartController::class, 'show'])->name('cart.show');
    Route::get('/thank-you', [CartController::class, 'checkout'])->name('cart.thank-you');
});