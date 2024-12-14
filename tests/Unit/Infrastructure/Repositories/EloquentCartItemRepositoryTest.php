<?php

namespace Tests\Unit\Infrastructure\Repositories;

use App\Domain\Cart\CartItem;
use App\Domain\Cart\Exceptions\CartItemNotFoundException;
use App\Domain\Shared\Exceptions\InvalidQuantityException;
use App\Domain\Shared\ValueObjects\UuidVO;
use App\Infrastructure\Repositories\EloquentCartItemRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Support\Factories\CartItemFactory;
use Tests\TestCase;

class EloquentCartItemRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testFindByIdOrFailReturnsCartItem(): void
    {
        $cartItem = CartItemFactory::new()->create();

        $repository = new EloquentCartItemRepository();
        $retrievedCartItem = $repository->findByIdOrFail($cartItem->id);

        $this->assertInstanceOf(CartItem::class, $retrievedCartItem);
        $this->assertEquals($cartItem->id, $retrievedCartItem->id->__toString());
        $this->assertEquals($cartItem->cartId->__toString(), $retrievedCartItem->cartId->__toString());
        $this->assertEquals($cartItem->quantity, $retrievedCartItem->quantity);
    }

    public function testFindByIdOrFailThrowsExceptionWhenCartItemNotFound(): void
    {
        $this->expectException(CartItemNotFoundException::class);

        $repository = new EloquentCartItemRepository();
        $repository->findByIdOrFail(UuidVO::generate());
    }

    public function testCreateSuccessfullyInsertsCartItem(): void
    {
        // Insertar un cart para asociarlo al cart item
        $cartId = UuidVO::generate()->__toString();
        DB::table('carts')->insert(['id' => $cartId, 'items' => json_encode([])]);

        // Insertar un producto
        $productId = UuidVO::generate()->__toString();
        DB::table('products')->insert([
            'id' => $productId,
            'name' => 'Bike',
            'price' => 1500.00,
            'stock' => 10,
        ]);

        $repository = new EloquentCartItemRepository();

        // Crear el cart item
        $quantity = 3;
        $cartItem = $repository->create(new UuidVO($cartId), new UuidVO($productId), $quantity);

        // Verificar que se ha creado correctamente
        $retrievedCartItem = DB::table('cart_items')->where('id', $cartItem->id->__toString())->first();

        $this->assertNotNull($retrievedCartItem);
        $this->assertEquals($cartId, $retrievedCartItem->cart_id);
        $this->assertEquals($productId, $retrievedCartItem->product_id);
        $this->assertEquals($quantity, $retrievedCartItem->quantity);
    }

    public function testCreateThrowsExceptionForInvalidQuantity(): void
    {
        $this->expectException(InvalidQuantityException::class);

        $repository = new EloquentCartItemRepository();
        $repository->create(UuidVO::generate(), UuidVO::generate(), -1);
    }

    public function testUpdateQuantitySuccessfullyChangesQuantity(): void
    {
        // Insertar un cart item
        $cartId = UuidVO::generate()->__toString();
        $productId = UuidVO::generate()->__toString();
        $cartItemId = UuidVO::generate()->__toString();

        DB::table('carts')->insert(['id' => $cartId, 'items' => json_encode([])]);
        DB::table('products')->insert([
            'id' => $productId,
            'name' => 'Bike',
            'price' => 1500.00,
            'stock' => 10,
        ]);
        DB::table('cart_items')->insert([
            'id' => $cartItemId,
            'cart_id' => $cartId,
            'product_id' => $productId,
            'quantity' => 1,
        ]);

        $repository = new EloquentCartItemRepository();
        $repository->updateQuantity(new UuidVO($cartItemId), 5);

        $updatedCartItem = DB::table('cart_items')->where('id', $cartItemId)->first();
        $this->assertEquals(5, $updatedCartItem->quantity);
    }

    public function testUpdateQuantityThrowsExceptionForInvalidQuantity(): void
    {
        $this->expectException(InvalidQuantityException::class);

        $repository = new EloquentCartItemRepository();
        $repository->updateQuantity(UuidVO::generate(), -1);
    }

    public function testDeleteSuccessfullyRemovesCartItem(): void
    {
        // Insertar un cart item
        $cartId = UuidVO::generate()->__toString();
        $productId = UuidVO::generate()->__toString();
        $cartItemId = UuidVO::generate()->__toString();

        DB::table('carts')->insert(['id' => $cartId, 'items' => json_encode([])]);
        DB::table('products')->insert([
            'id' => $productId,
            'name' => 'Bike',
            'price' => 1500.00,
            'stock' => 10,
        ]);
        DB::table('cart_items')->insert([
            'id' => $cartItemId,
            'cart_id' => $cartId,
            'product_id' => $productId,
            'quantity' => 1,
        ]);

        $repository = new EloquentCartItemRepository();
        $repository->delete(new UuidVO($cartItemId));

        $deletedCartItem = DB::table('cart_items')->where('id', $cartItemId)->first();
        $this->assertNull($deletedCartItem);
    }

    public function testSaveInsertsOrUpdatesCartItem(): void
    {
        $cartId = UuidVO::generate()->__toString();
        $productId = UuidVO::generate()->__toString();
        $cartItemId = UuidVO::generate()->__toString();

        DB::table('carts')->insert(['id' => $cartId, 'items' => json_encode([])]);
        DB::table('products')->insert([
            'id' => $productId,
            'name' => 'Bike',
            'price' => 1500.00,
            'stock' => 10,
        ]);

        $cartItemData = (object) [
            'id' => $cartItemId,
            'cart_id' => $cartId,
            'product_id' => $productId,
            'quantity' => 3,
        ];

        $cartItem = CartItem::fromDatabase($cartItemData);

        $repository = new EloquentCartItemRepository();

        $repository->save($cartItem);

        $retrieved = DB::table('cart_items')->where('id', $cartItemId)->first();
        $this->assertNotNull($retrieved);
        $this->assertEquals($cartId, $retrieved->cart_id);
        $this->assertEquals($productId, $retrieved->product_id);
        $this->assertEquals(3, $retrieved->quantity);

        $updatedCartItemData = (object) [
            'id' => $cartItemId,
            'cart_id' => $cartId,
            'product_id' => $productId,
            'quantity' => 5,
        ];

        $updatedCartItem = CartItem::fromDatabase($updatedCartItemData);

        $repository->save($updatedCartItem);

        $updated = DB::table('cart_items')->where('id', $cartItemId)->first();
        $this->assertNotNull($updated);
        $this->assertEquals(5, $updated->quantity); // Nueva cantidad
        $this->assertEquals($cartId, $updated->cart_id);
        $this->assertEquals($productId, $updated->product_id);
    }
}