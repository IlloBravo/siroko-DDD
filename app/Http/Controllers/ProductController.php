<?php

namespace App\Http\Controllers;

use App\Domain\Product\Repository\ProductRepositoryInterface;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {}

    public function index(): View
    {
        $products = $this->productRepository->findAll();

        return view('product.index', compact('products'));
    }
}
