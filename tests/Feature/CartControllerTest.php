<?php

namespace Tests\Feature;

use App\Domain\Cart\Cart;
use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test para agregar productos al carrito.
     */
    public function testAddCartItem()
    {
        $cartId = UuidVO::generate();
        $cart = Cart::fromDatabase((object)[
            'id' => $cartId->__toString(),
            'items' => json_encode([]),
        ]);

        DB::table('carts')->insert([
            'id' => $cart->id->__toString(),
            'items' => $cart->getCartItems(),
        ]);

        $product1 = Product::fromDatabase((object)[
            'id' => UuidVO::generate()->__toString(),
            'name' => 'Laptop',
            'price' => 1500.00,
            'stock' => 10,
        ]);
        DB::table('products')->insert([
            'id' => $product1->id->__toString(),
            'name' => $product1->name,
            'price' => $product1->price,
            'stock' => $product1->stock,
        ]);

        $payload = [
            'products' => [
                ['id' => $product1->id->__toString(), 'quantity' => 1],
            ],
        ];

        $response = $this->postJson(route('api.cart.addCartItem', [
            'cartId' => $cart->id->__toString()
        ]), $payload);

        $response->assertStatus(200);
        $response->assertJson(['message' => __('Cart.products_added')]);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id->__toString(),
            'product_id' => $product1->id->__toString(),
            'quantity' => 1,
        ]);
    }
}