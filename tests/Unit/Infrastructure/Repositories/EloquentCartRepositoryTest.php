<?php

namespace Tests\Unit\Infrastructure\Repositories;

use App\Domain\Cart\Cart;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use App\Infrastructure\Repositories\EloquentCartRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EloquentCartRepositoryTest extends TestCase
{
    use RefreshDatabase;
    public function testSaveSuccessfullyStoresCart(): void
    {
        $cartId = UuidVO::generate();
        $product = new Product(
            UuidVO::generate(),
            'Product A',
            10.5,
            100,
            0
        );

        $cart = new Cart(
            $cartId,
            collect([$product]),
            now(),
            now()
        );

        $repository = new EloquentCartRepository();
        $repository->save($cart);

        $cartData = DB::table('carts')->where('id', (string) $cart->id)->first();
        $this->assertNotNull($cartData);
        $this->assertEquals($cart->id, $cartData->id);
        $this->assertEquals(json_encode([
            [
                'id' => (string) $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->quantity,
                'cartQuantity' => $product->cartQuantity,
            ]
        ]), $cartData->items);
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function testFindByIdOrFailReturnsCart(): void
    {
        $cartId = UuidVO::generate();
        $product = new Product(
            UuidVO::generate(),
            'Product A',
            10.5,
            100,
            0
        );

        $cart = new Cart(
            $cartId,
            collect([$product]),
            now(),
            now()
        );

        DB::table('carts')->insert([
            'id' => (string) $cart->id,
            'items' => json_encode([
                [
                    'id' => (string) $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                    'cartQuantity' => $product->cartQuantity,
                ]
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $repository = new EloquentCartRepository();
        $retrievedCart = $repository->findByIdOrFail($cart->id);

        $this->assertInstanceOf(Cart::class, $retrievedCart);
        $this->assertEquals($cart->id, $retrievedCart->id);
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function testFindByIdOrFailThrowsExceptionWhenCartNotFound(): void
    {
        $cartId = UuidVO::generate();

        $this->expectException(CartNotFoundException::class);

        $repository = new EloquentCartRepository();
        $repository->findByIdOrFail($cartId);
    }

    public function testDeleteSuccessfullyDeletesCart(): void
    {
        $cartId = UuidVO::generate();
        $product = new Product(
            UuidVO::generate(),
            'Product A',
            10.5,
            100,
            0
        );

        $cart = new Cart(
            $cartId,
            collect([$product]),
            now(),
            now()
        );

        DB::table('carts')->insert([
            'id' => (string) $cart->id,
            'items' => json_encode([
                [
                    'id' => (string) $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                    'cartQuantity' => $product->cartQuantity,
                ]
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $repository = new EloquentCartRepository();
        $repository->delete((string) $cart->id);

        $cartData = DB::table('carts')->where('id', (string) $cart->id)->first();
        $this->assertNull($cartData);
    }

    public function testFindAllReturnsAllCarts(): void
    {
        DB::table('carts')->truncate();

        $cartId1 = UuidVO::generate();
        $product1 = new Product(
            UuidVO::generate(),
            'Product A',
            10.5,
            100,
            0
        );

        $cart1 = new Cart(
            $cartId1,
            collect([$product1]),
            now(),
            now()
        );

        DB::table('carts')->insert([
            'id' => (string) $cart1->id,
            'items' => json_encode([
                [
                    'id' => (string) $product1->id,
                    'name' => $product1->name,
                    'price' => $product1->price,
                    'quantity' => $product1->quantity,
                    'cartQuantity' => $product1->cartQuantity,
                ]
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cartId2 = UuidVO::generate();
        $product2 = new Product(
            UuidVO::generate(),
            'Product B',
            20.0,
            50,
            1
        );

        $cart2 = new Cart(
            $cartId2,
            collect([$product2]),
            now(),
            now()
        );

        DB::table('carts')->insert([
            'id' => (string) $cart2->id,
            'items' => json_encode([
                [
                    'id' => (string) $product2->id,
                    'name' => $product2->name,
                    'price' => $product2->price,
                    'quantity' => $product2->quantity,
                    'cartQuantity' => $product2->cartQuantity,
                ]
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $repository = new EloquentCartRepository();
        $carts = $repository->findAll();

        $this->assertCount(2, $carts);
        $this->assertEquals($cart1->id, $carts[0]->id);
        $this->assertEquals($cart2->id, $carts[1]->id);
    }
}
