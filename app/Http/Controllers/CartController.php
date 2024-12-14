<?php

namespace App\Http\Controllers;

use App\Application\Cart\UseCases\AddProductToCartUseCase;
use App\Application\Cart\UseCases\UpdateCartItemQuantityUseCase;
use App\Application\Cart\UseCases\RemoveProductFromCartUseCase;
use App\Application\Cart\UseCases\GetTotalProductsUseCase;
use App\Application\Cart\UseCases\CheckoutCartUseCase;
use App\Domain\Cart\Exceptions\CartItemNotFoundException;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Exceptions\InsufficientStockException;
use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Shared\ValueObjects\UuidVO;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly AddProductToCartUseCase       $addProductToCartUseCase,
        private readonly UpdateCartItemQuantityUseCase $updateCartItemQuantityUseCase,
        private readonly RemoveProductFromCartUseCase  $removeProductFromCartUseCase,
        private readonly GetTotalProductsUseCase       $getTotalProductsUseCase,
        private readonly CheckoutCartUseCase           $checkoutCartUseCase,
        private readonly CartRepositoryInterface       $cartRepository
    ) {}

    public function addCartItem(Request $request, string $cartId): JsonResponse
    {
        $validatedData = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|uuid',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        foreach ($validatedData['products'] as $product) {
            $this->addProductToCartUseCase->execute(
                $cartId,
                $product['id'],
                $product['quantity']
            );
        }

        return response()->json([
            'message' => __('Cart.products_added'),
        ]);
    }

    public function updateCart(Request $request, string $cartId): JsonResponse|RedirectResponse
    {
        $cartItemsData = $request->input('products');

        try {
            foreach ($cartItemsData as $cartItemId => $cartItemData) {
                $quantity = (int) $cartItemData['quantity'];
                $this->updateCartItemQuantityUseCase->execute($cartId, $cartItemId, $quantity);
            }

            return response()->json(['message' => __('Cart.cart_updated')]);

        } catch (InsufficientStockException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function removeCartItem(string $cartId, string $cartItemId): JsonResponse
    {
        try {
            $this->removeProductFromCartUseCase->execute($cartId, $cartItemId);
            return response()->json(['message' => __('Cart.item_removed')]);
        } catch (CartNotFoundException|CartItemNotFoundException|ProductNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function getTotalProducts(string $cartId): JsonResponse
    {
        $total = $this->getTotalProductsUseCase->execute($cartId);

        return response()->json(['total_products' => $total]);
    }

    public function checkout(string $cartId): View
    {
        $this->checkoutCartUseCase->execute($cartId);

        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));

        return view('cart.thank-you', compact('cart'));
    }

    public function show(string $cartId): View
    {
        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));
        return view('cart.show', compact('cart'));
    }
}
