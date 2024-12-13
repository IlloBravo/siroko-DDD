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

        $cart = $this->cartRepository->findAll()->first();

        dump($products, $cart);

        return view('product.index', compact('products', 'cart'));
    }
}
