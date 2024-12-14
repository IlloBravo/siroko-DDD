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
    public function testFindByIdOrFailReturnsCart(): void
    {
        $cartId = UuidVO::generate();
        $cartData = (object)[
            'id' => $cartId,
            'items' => json_encode([]),
        ];

        $cart = Cart::fromDatabase($cartData);

        DB::table('carts')->insert([
            'id' => (string) $cart->id,
            'items' => $cart->getCartItems()
        ]);

        $repository = new EloquentCartRepository();
        $retrievedCart = $repository->findByIdOrFail($cart->id);

        $this->assertInstanceOf(Cart::class, $retrievedCart);
        $this->assertEquals($cart->id, $retrievedCart->id);
    }

    public function testFindByIdOrFailThrowsExceptionWhenCartNotFound(): void
    {
        $cartId = UuidVO::generate();

        $this->expectException(CartNotFoundException::class);

        $repository = new EloquentCartRepository();
        $repository->findByIdOrFail($cartId);
    }

    public function testFindAllReturnsCollection(): void
    {
        $cartId = UuidVO::generate();
        $cartData = (object)[
            'id' => $cartId,
            'items' => json_encode([]),
        ];

        $cart = Cart::fromDatabase($cartData);

        DB::table('carts')->insert([
            'id' => (string) $cart->id,
            'items' => $cart->getCartItems()
        ]);

        $cartId2 = UuidVO::generate();
        $cartData2 = (object)[
            'id' => $cartId2,
            'items' => json_encode([]),
        ];

        $cart2 = Cart::fromDatabase($cartData2);

        DB::table('carts')->insert([
            'id' => (string) $cart2->id,
            'items' => $cart2->getCartItems()
        ]);

        $repository = new EloquentCartRepository();
        $retrieveCarts = $repository->findAll();

        foreach ($retrieveCarts as $cartFromCollection) {
            $this->assertInstanceOf(Cart::class, $cartFromCollection);
        }
    }

    public function testSaveSuccessfullyUpdatesCart(): void
    {
        $productData = (object) [
            'id' => UuidVO::generate()->__toString(),
            'name' => 'Bike',
            'price' => 1500.00,
            'stock' => 10,
        ];

        $product = Product::fromDatabase($productData);
        $cartId = UuidVO::generate();

        $cartItemData = (object) [
            'id' => UuidVO::generate()->__toString(),
            'cartId' => $cartId->__toString(),
            'productId' => $product->id->__toString(),
            'quantity' => 1,
        ];


        $cartData = (object)[
            'id' => $cartId,
            'items' => json_encode([$cartItemData]),
        ];

        $cart = Cart::fromDatabase($cartData);

        $repository = new EloquentCartRepository();
        $repository->save($cart);
        $retrievedCart = $repository->findByIdOrFail($cart->id);
        $this->assertEquals(1, $retrievedCart->getCartItems()->first()->quantity);
    }
}
