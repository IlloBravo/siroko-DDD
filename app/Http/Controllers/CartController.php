<?php

namespace App\Http\Controllers;

use App\Application\Cart\UseCases\AddProductToCartUseCase;
use App\Application\Cart\UseCases\UpdateProductQuantityUseCase;
use App\Application\Cart\UseCases\RemoveProductFromCartUseCase;
use App\Application\Cart\UseCases\GetTotalProductsUseCase;
use App\Application\Cart\UseCases\CheckoutCartUseCase;
use App\Domain\Product\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct(
        private readonly AddProductToCartUseCase      $addProductToCartUseCase,
        private readonly UpdateProductQuantityUseCase $updateProductQuantityUseCase,
        private readonly RemoveProductFromCartUseCase $removeProductFromCartUseCase,
        private readonly GetTotalProductsUseCase      $getTotalProductsUseCase,
        private readonly CheckoutCartUseCase          $checkoutCartUseCase
    ) {}

    /**
     * @throws Exception
     */
    public function addProduct(Request $request, string $cartId): JsonResponse
    {
        $product =  Product::fromArray([
            $request->input('id'),
            $request->input('name'),
            $request->input('price'),
            $request->input('quantity')
        ]);

        $this->addProductToCartUseCase->execute($cartId, $product);

        return response()->json(['message' => 'Product added to cart']);
    }

    /**
     * @throws Exception
     */
    public function updateProduct(Request $request, string $cartId, string $productId): JsonResponse
    {
        $this->updateProductQuantityUseCase->execute($cartId, $productId, $request->input('quantity'));

        return response()->json(['message' => 'Product quantity updated']);
    }

    /**
     * @throws Exception
     */
    public function removeProduct(string $cartId, string $productId): JsonResponse
    {
        $this->removeProductFromCartUseCase->execute($cartId, $productId);

        return response()->json(['message' => 'Product removed from cart']);
    }

    /**
     * @throws Exception
     */
    public function getTotalProducts(string $cartId): JsonResponse
    {
        $total = $this->getTotalProductsUseCase->execute($cartId);

        return response()->json(['total_products' => $total]);
    }

    /**
     * @throws Exception
     */
    public function checkout(string $cartId): JsonResponse
    {
        $this->checkoutCartUseCase->execute($cartId);

        return response()->json(['message' => 'Cart checked out successfully']);
    }
}
