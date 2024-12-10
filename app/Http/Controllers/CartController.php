<?php

namespace App\Http\Controllers;

use App\Application\Cart\UseCases\AddProductToCartUseCase;
use App\Application\Cart\UseCases\UpdateProductQuantityUseCase;
use App\Application\Cart\UseCases\RemoveProductFromCartUseCase;
use App\Application\Cart\UseCases\GetTotalProductsUseCase;
use App\Application\Cart\UseCases\CheckoutCartUseCase;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Repository\ProductRepositoryInterface;
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
        private readonly CheckoutCartUseCase          $checkoutCartUseCase,
        private readonly ProductRepositoryInterface   $productRepository,
        private readonly CartRepositoryInterface      $cartRepository
    ) {}

    /**
     * @throws ProductNotFoundException
     * @throws CartNotFoundException
     */
    public function addProduct(Request $request): JsonResponse
    {
        $product = $this->productRepository->findByIdOrFail($request->input('id'));
        $cart = $this->cartRepository->findByIdOrFail($request->input('cart_id'));

        $this->addProductToCartUseCase->execute(
            $cart,
            $product,
            $request->input('quantity')
        );

        return response()->json([
            'message' => __('Cart.products_added')
        ]);
    }

    /**
     * @throws Exception
     */
    public function updateProduct(Request $request, string $cartId, string $productId): JsonResponse
    {
        $this->updateProductQuantityUseCase->execute($cartId, $productId, $request->input('quantity'));

        return response()->json([
            'message' => __('Cart.product_updated', [
                'quantity' => $request->input('quantity')
            ])
        ]);
    }

    /**
     * @throws Exception
     */
    public function removeProduct(string $cartId, string $productId): JsonResponse
    {
        $this->removeProductFromCartUseCase->execute($cartId, $productId);

        return response()->json([
            'message' => __('Cart.product_removed')
        ]);
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

        return response()->json([
            'message' => __('Cart.cart_checked_out')
        ]);
    }
}
