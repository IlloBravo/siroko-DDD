<?php

namespace App\Http\Controllers;

use App\Application\Cart\UseCases\AddProductToCartUseCase;
use App\Application\Cart\UseCases\UpdateProductQuantityUseCase;
use App\Application\Cart\UseCases\RemoveProductFromCartUseCase;
use App\Application\Cart\UseCases\GetTotalProductsUseCase;
use App\Application\Cart\UseCases\CheckoutCartUseCase;
use App\Domain\Cart\Cart;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;
use DateMalformedStringException;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Ramsey\Uuid\Uuid;

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
        $product = $this->productRepository->findByIdOrFail( UuidVO::fromString($request->input('id')));
        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($request->input('cart_id')));

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
        $this->updateProductQuantityUseCase->execute(
            $cartId,
            $productId,
            $request->input('quantity')
        );

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
        $this->removeProductFromCartUseCase->execute(
            $cartId,
            $productId
        );

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

    public function index(): View
    {
        $carts = $this->cartRepository->findAll();

        $cartId = session('cart_id');

        return view('cart.index', compact('cartId', 'carts'));
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

    /**
     * @throws DateMalformedStringException
     */
    public function createCart(Request $request): RedirectResponse
    {
        $cartId = (string) Uuid::uuid4();

        $cart = Cart::create([
            'id' => $cartId,
            'items' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->cartRepository->save($cart);

        $productId = UuidVO::fromString($request->input('id'));

        $product = $this->productRepository->findByIdOrFail($productId);
        $quantity = $request->input('quantity');

        $this->addProductToCartUseCase->execute($cart, $product, $quantity);
        $this->cartRepository->save($cart);

        return redirect()->route('cart.show', ['cartId' => $cartId])
            ->with('success', __('Cart.cart_created_with_product'));
    }
}
