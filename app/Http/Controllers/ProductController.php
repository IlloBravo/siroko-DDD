<?php

namespace App\Http\Controllers;

use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CartRepositoryInterface $cartRepository
    ) {}

    public function index(): View
    {
        $products = $this->productRepository->findAll();
        $carts = $this->cartRepository->findAll();

        $cartId = session('cart_id'); // Asumimos que el cartId está almacenado en la sesión

        return view('cart.index',
            compact('products', 'cartId'),
        compact('carts')
        );
    }
}
