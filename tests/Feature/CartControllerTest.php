<?php

use App\Domain\Shared\ValueObjects\UuidVO;
use App\Infrastructure\Repositories\EloquentCartRepository;
use App\Infrastructure\Repositories\EloquentProductRepository;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    public function test_add_product_to_cart()
    {
        $productRepository = new EloquentProductRepository();
        $products = $productRepository->findAll();

        $cartRepository = new EloquentCartRepository();
        $carts = $cartRepository->findAll();

        $response = $this->postJson(route('api.cart.addProduct', ['cartId' => $carts[0]->id->__toString()]), [
            'id' => $products[0]->id->__toString(),
            'cart_id' => $carts[0]->id->__toString(),
            'quantity' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => __('Cart.products_added')]);

        $this->assertDatabaseHas('carts', [
            'id' => $carts[0]->id,
        ]);
    }

    public function test_update_product_quantity_in_cart()
    {
        $productRepository = new EloquentProductRepository();
        $products = $productRepository->findAll();

        $cartRepository = new EloquentCartRepository();
        $carts = $cartRepository->findAll();

        $cartId = $carts[0]->id->__toString();
        $productId = $products[0]->id->__toString();

        $response = $this->putJson(route('api.cart.updateProduct', ['cartId' => $cartId]), [
            'products' => [
                $productId => [
                    'quantity' => 5,
                ],
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => __('Cart.cart_updated')]);

        $this->assertDatabaseHas('carts', [
            'id' => $cartId,
        ]);
    }

    /**
     * @throws DateMalformedStringException
     */
    public function test_update_product_quantity_in_cart_redirect_with_success_message()
    {
        $productRepository = new EloquentProductRepository();
        $products = $productRepository->findAll();

        $cartRepository = new EloquentCartRepository();
        $carts = $cartRepository->findAll();

        $cartId = $carts[0]->id->__toString();
        $productId = $products[0]->id->__toString();

        $response = $this->put(route('api.cart.updateProduct', ['cartId' => $cartId]), [
            'products' => [
                $productId => [
                    'quantity' => 5,
                ],
            ],
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('cart.show', ['cartId' => $cartId]));

        $response->assertSessionHas('success', __('Cart.cart_updated'));

        $cart = $cartRepository->findByIdOrFail(UuidVO::fromString($cartId));
        $items = json_decode($cart->items, true);

        $this->assertEquals(5, $items[0]['cartQuantity']);
    }

    /**
     * @throws DateMalformedStringException
     */
    public function test_update_product_quantity_in_cart_redirect_with_error_message_due_to_insufficient_stock()
    {
        $productRepository = new EloquentProductRepository();
        $products = $productRepository->findAll();

        $cartRepository = new EloquentCartRepository();
        $carts = $cartRepository->findAll();

        $cartId = $carts[0]->id->__toString();
        $productId = $products[0]->id->__toString();

        $response = $this->put(route('api.cart.updateProduct', ['cartId' => $cartId]), [
            'products' => [
                $productId => [
                    'quantity' => 150,
                ],
            ],
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('cart.show', ['cartId' => $cartId]));

        $response->assertSessionHas('error', 'Stock insuficiente para el Producto con ID ' . $productId);

        $cart = $cartRepository->findByIdOrFail(UuidVO::fromString($cartId));
        $items = json_decode($cart->items, true);

        $this->assertNotEquals($products[0]->quantity + 1, $items[0]['cartQuantity']);
    }

    /**
     * @throws DateMalformedStringException
     */
    public function test_remove_product_from_cart()
    {
        $productRepository = new EloquentProductRepository();
        $products = $productRepository->findAll();

        $cartRepository = new EloquentCartRepository();
        $carts = $cartRepository->findAll();

        $cartId = $carts[0]->id->__toString();
        $productId = $products[0]->id->__toString();

        $response = $this->deleteJson(route('api.cart.removeProduct', [
            'cartId' => $cartId,
            'productId' => $productId
        ]));

        $response->assertStatus(302);

        $response->assertRedirect(route('cart.index'));

        $response->assertSessionHas('success', __('Cart.cart_checked_out'));

        $cart = $cartRepository->findByIdOrFail(UuidVO::fromString($cartId));

        $items = json_decode($cart->items, true);

        $this->assertNotContains($productId, array_column($items, 'id'));
    }

    public function test_get_total_products_in_cart()
    {
        $cartRepository = new EloquentCartRepository();
        $carts = $cartRepository->findAll();

        $cartId = $carts[0]->id->__toString();

        $productsFromCart = json_decode($carts[0]->items, true);
        $totalExpected = 0;
        foreach ($productsFromCart as $product) {
            $totalExpected += $product['cartQuantity'];
        }

        $response = $this->getJson(route('api.cart.getTotalProducts', ['cartId' => $cartId]));

        $response->assertStatus(200);

        $response->assertJson([
            'total_products' => $totalExpected,
        ]);
    }

    /**
     * @throws DateMalformedStringException
     */
    public function test_checkout_cart()
    {
        $cartRepository = new EloquentCartRepository();
        $carts = $cartRepository->findAll();

        $cartId = $carts[0]->id->__toString();

        $response = $this->postJson(route('api.cart.checkout', ['cartId' => $cartId]));

        $response->assertStatus(302);

        $response->assertRedirect(route('cart.show', ['cartId' => $cartId]));

        $response->assertSessionHas('success', __('Cart.cart_checked_out'));

        $cart = $cartRepository->findByIdOrFail(UuidVO::fromString($cartId));
        $emptyItemsFromCart = json_decode($cart->items, true);

        $this->assertEmpty($emptyItemsFromCart);
    }

    public function test_index_cart()
    {
        $cartRepository = new EloquentCartRepository();
        $carts = $cartRepository->findAll();

        $cartId = $carts[0]->id->__toString();
        session(['cart_id' => $cartId]);

        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cart.index');

        $response->assertViewHas('cartId', $cartId);
        $response->assertViewHas('carts', $carts);

        $response->assertSee($carts[0]->id);
    }

    public function test_show_cart()
    {
        $cartRepository = new EloquentCartRepository();
        $carts = $cartRepository->findAll();

        $cartId = $carts[0]->id->__toString();

        $response = $this->get(route('cart.show', ['cartId' => $cartId]));

        $response->assertStatus(200);
        $response->assertViewIs('cart.show');

        $response->assertViewHas('cart');
        $response->assertSee($cartId);
    }

    public function test_thankyou_page()
    {
        $cartRepository = new EloquentCartRepository();
        $carts = $cartRepository->findAll();

        $cartId = $carts[0]->id->__toString();

        $response = $this->get(route('cart.thankyou', ['cartId' => $cartId]));

        $response->assertStatus(200);
        $response->assertViewIs('cart.thankyou');
    }

    public function test_create_cart_with_products()
    {
        $productRepository = new EloquentProductRepository();
        $products = $productRepository->findAll();

        $response = $this->postJson(route('api.cart.createCart'), [
            'products' => [
                [
                    'id' => $products[0]->id->__toString(),
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertStatus(200);

        $responseContent = json_decode($response->getContent(), true);

        $this->assertDatabaseHas('carts', [
            'id' => $responseContent['cartId'],
        ]);
    }
}
