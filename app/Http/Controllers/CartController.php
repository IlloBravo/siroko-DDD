<?php

namespace App\Http\Controllers;

use App\Application\Cart\UseCases\AddProductToCartUseCase;
use App\Application\Cart\UseCases\UpdateProductQuantityUseCase;
use App\Application\Cart\UseCases\RemoveProductFromCartUseCase;
use App\Application\Cart\UseCases\GetTotalProductsUseCase;
use App\Application\Cart\UseCases\CheckoutCartUseCase;
use App\Domain\Cart\CartItem;
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

    /**
     * @throws CartNotFoundException
     */
    public function addProduct(Request $request, string $cartId): JsonResponse
    {
        $productsData = $this->validateProductsData($request);

        if (!$productsData) {
            return $this->invalidProductsResponse();
        }

        $cartItems = $this->createCartItemsFromData($productsData);
        $this->addCartItemsToCart($cartId, $cartItems);

        return $this->ProductAddedSuccessResponse($cartItems);
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

    private function createCartItemsFromData(array $productsData): array
    {
        return array_map(function ($productData) {
            $product = Product::fromArray($productData);
            return CartItem::fromProduct($product, $product->quantity);
        }, $productsData);
    }

    /**
     * @throws CartNotFoundException
     */
    private function addCartItemsToCart(string $cartId, array $cartItems): void
    {
        foreach ($cartItems as $cartItem) {
            $this->addProductToCartUseCase->execute($cartId, $cartItem);
        }
    }

    private function ProductAddedSuccessResponse(array $cartItems): JsonResponse
    {
        $productNames = array_map(fn($cartItem) => $cartItem->product->name, $cartItems);

        return response()->json([
            'message' => __('Cart.products_added', ['names' => implode(', ', $productNames)])
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
