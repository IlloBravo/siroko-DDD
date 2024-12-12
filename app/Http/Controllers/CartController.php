<?php

namespace App\Http\Controllers;

use App\Application\Cart\UseCases\AddProductToCartUseCase;
use App\Application\Cart\UseCases\UpdateProductQuantityUseCase;
use App\Application\Cart\UseCases\RemoveProductFromCartUseCase;
use App\Application\Cart\UseCases\GetTotalProductsUseCase;
use App\Application\Cart\UseCases\CheckoutCartUseCase;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Exceptions\InsufficientStockException;
use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

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
    public function addProduct(Request $request, string $cartId): JsonResponse
    {
        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));

        $products = $request->get('products');

        foreach ($products as $product) {
            if ($product['quantity'] > 0) {
                $this->addProductToCartUseCase->execute(
                    $cart,
                    $this->productRepository->findByIdOrFail(UuidVO::fromString($product['id'])),
                    $product['quantity']
                );
            }
        }

        return response()->json([
            'message' => __('Cart.products_added')
        ]);
    }

    /**
     * @throws CartNotFoundException
     */
    public function updateProduct(Request $request, string $cartId): JsonResponse|RedirectResponse
    {
        $productsData = $request->input('products');

        try {
            foreach ($productsData as $productId => $productData) {
                $quantity = (int) $productData['quantity'];
                $this->updateProductQuantityUseCase->execute($cartId, $productId, $quantity);
            }

            if ($request->ajax()) {
                return response()->json(['message' => __('Cart.cart_updated')]);
            }

            return redirect()->route('cart.show', ['cartId' => $cartId])
                ->with('success', __('Cart.cart_updated'));

        } catch (InsufficientStockException $e) {
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }

            return redirect()->route('cart.show', ['cartId' => $cartId])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function removeProduct(string $cartId, string $productId): RedirectResponse
    {
        $this->removeProductFromCartUseCase->execute($cartId, $productId);

        return redirect()->route('cart.index')
            ->with('success', __('Cart.cart_checked_out'));
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
    public function checkout(string $cartId): RedirectResponse
    {
        $this->checkoutCartUseCase->execute($cartId);

        return redirect()->route('cart.show', ['cartId' => $cartId])
            ->with('success', __('Cart.cart_checked_out'));
    }

    public function index(): View
    {
        $carts = $this->cartRepository->findAll();

        return view('cart.index', compact('carts'));
    }

    public function show(string $cartId): View
    {
        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));
        return view('cart.show', compact('cart'));
    }

    public function thankYou(): View
    {
        return view('cart.thankyou');
    }
}
