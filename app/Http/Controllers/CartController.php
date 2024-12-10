<?php

namespace App\Http\Controllers;

use App\Application\Cart\UseCases\AddProductToCartUseCase;
use App\Application\Cart\UseCases\UpdateProductQuantityUseCase;
use App\Application\Cart\UseCases\RemoveProductFromCartUseCase;
use App\Application\Cart\UseCases\GetTotalProductsUseCase;
use App\Application\Cart\UseCases\CheckoutCartUseCase;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Product\Product;
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

    public function addProduct(Request $request, string $cartId): JsonResponse
    {
        $productsData = $this->validateProductsData($request);

        if (!$productsData) {
            return $this->invalidProductsResponse();
        }

        $products = $this->createProductsFromData($productsData);
        $this->addProductsToCart($cartId, $products);

        return $this->successResponse($products);
    }

    /**
     * @throws CartNotFoundException
     */
    public function updateProduct(Request $request, string $cartId, string $productId): JsonResponse
    {
        $this->updateProductQuantityUseCase->execute(
            $cartId,
            $productId,
            $request->input('quantity')
        );

        return response()->json([
            'message' => __('Cart.product_updated', [
                'name' => $request->input('name'),
                'quantity' => $request->input('quantity')
            ])
        ]);
    }

    /**
     * @throws CartNotFoundException
     */
    public function removeProduct(Request $request, string $cartId, string $productId): JsonResponse
    {
        $this->removeProductFromCartUseCase->execute($cartId, $productId);

        return response()->json([
            'message' => __('Cart.product_removed', ['name' => $request->input('name')])
        ]);
    }

    /**
     * @throws CartNotFoundException
     */
    public function getTotalProducts(string $cartId): JsonResponse
    {
        $total = $this->getTotalProductsUseCase->execute($cartId);

        return response()->json(['total_products' => $total]);
    }

    /**
     * @throws CartNotFoundException
     */
    public function checkout(string $cartId): JsonResponse
    {
        $this->checkoutCartUseCase->execute($cartId);

        return response()->json([
            'message' => __('Cart.cart_checked_out')
        ]);
    }

    private function validateProductsData(Request $request): ?array
    {
        $productsData = $request->input('products');

        return is_array($productsData) ? $productsData : null;
    }

    private function invalidProductsResponse(): JsonResponse
    {
        return response()->json([
            'error' => __('Cart.invalid_products_data')
        ], 400);
    }

    private function createProductsFromData(array $productsData): array
    {
        return array_map(fn($productData) => Product::fromArray($productData), $productsData);
    }

    /**
     * @throws CartNotFoundException
     */
    private function addProductsToCart(string $cartId, array $products): void
    {
        foreach ($products as $product) {
            $this->addProductToCartUseCase->execute($cartId, $product);
        }
    }

    private function successResponse(array $products): JsonResponse
    {
        $productNames = array_map(fn($product) => $product->name, $products);

        return response()->json([
            'message' => __('Cart.products_added', ['names' => implode(', ', $productNames)])
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
