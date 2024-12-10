<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::get('/cart/{cartId}', [CartController::class, 'show'])->name('cart.show');

Route::get('/cart/{cartId}/thankyou', [CartController::class, 'thankYou'])->name('cart.thankyou');
