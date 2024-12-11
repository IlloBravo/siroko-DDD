<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect('/all-products-available');
});

Route::get('/all-products-available', [ProductController::class, 'index'])->name('products.index');

Route::get('/all-carts-created', [CartController::class, 'index'])->name('cart.index');

Route::get('/cart/{cartId}/view', [CartController::class, 'show'])->name('cart.show');

Route::get('/cart/{cartId}/thank-you', [CartController::class, 'thankYou'])->name('cart.thankyou');
