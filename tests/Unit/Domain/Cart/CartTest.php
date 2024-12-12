<?php

namespace Tests\Unit\Domain\Cart;

use App\Domain\Cart\Cart;
use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use DateMalformedStringException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CartTest extends TestCase
{
    /**
     * @throws DateMalformedStringException
     */
    public function testAddProduct()
    {
        $cartData = [
            'id' => Uuid::uuid4(),
            'items' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $cart = Cart::create($cartData);

        $productData = (object) [
            'id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Test Product',
            'price' => 100.0,
            'quantity' => 10,
            'cartQuantity' => 0,
        ];
        $product = Product::fromDatabase($productData);

        $cart->addProduct($product, 2);

        $this->assertEquals(1, $cart->items->count());
        $this->assertEquals(8, $product->stock);

        $cart->addProduct($product, 3);
        $this->assertEquals(5, $product->stock);
        $this->assertEquals(5, $cart->getProductQuantity(UuidVO::fromString($product->id)));
    }

    /**
     * @throws DateMalformedStringException
     */
    public function testUpdateProductQuantity()
    {
        $cartData = [
            'id' => Uuid::uuid4(),
            'items' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $cart = Cart::create($cartData);

        $productData = (object) [
            'id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Gafas de Sol',
            'price' => 59.99,
            'quantity' => 10,
            'cartQuantity' => 0,
        ];
        $product = Product::fromDatabase($productData);

        $cart->addProduct($product, 2);
        $cart->updateProductQuantity(UuidVO::fromString($product->id), 4);

        $this->assertEquals(6, $product->stock);
        $this->assertEquals(4, $cart->getProductQuantity(UuidVO::fromString($product->id)));

        $cart->updateProductQuantity(UuidVO::fromString($product->id), 1);
        $this->assertEquals(9, $product->stock);
        $this->assertEquals(1, $cart->getProductQuantity(UuidVO::fromString($product->id)));
    }

    /**
     * @throws DateMalformedStringException
     */
    public function testRemoveProduct()
    {
        $cartData = [
            'id' => Uuid::uuid4(),
            'items' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $cart = Cart::create($cartData);

        $productData = (object) [
            'id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Producto B',
            'price' => 30.0,
            'quantity' => 20,
            'cartQuantity' => 0,
        ];
        $product = Product::fromDatabase($productData);

        $cart->addProduct($product, 5);

        $this->assertEquals(5, $cart->getProductQuantity(UuidVO::fromString($product->id)));

        $cart->removeProduct(UuidVO::fromString($product->id));

        $this->assertEquals(20, $product->stock);
        $this->assertEquals(0, $cart->getProductQuantity(UuidVO::fromString($product->id)));
    }

    public function testFromDatabase()
    {
        $cartData = (object) [
            'id' => '123e4567-e89b-12d3-a456-426614174000',
            'items' => json_encode([]),
            'created_at' => '2024-12-10 12:00:00',
            'updated_at' => '2024-12-10 12:00:00',
        ];

        $cart = Cart::fromDatabase($cartData);

        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', (string) $cart->id);
    }

    public function testGetTotalProducts()
    {
        $cartData = [
            'id' => Uuid::uuid4(),
            'items' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $cart = Cart::create($cartData);

        $productData = (object) [
            'id' => Uuid::uuid4(),
            'name' => 'Camiseta',
            'price' => 20.0,
            'quantity' => 15,
            'cartQuantity' => 0,
        ];
        $product = Product::fromDatabase($productData);

        $cart->addProduct($product, 3);

        $this->assertEquals(3, $cart->getTotalProducts());
    }

    public function testGetProductQuantity()
    {
        $cartData = [
            'id' => Uuid::uuid4(),
            'items' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $cart = Cart::create($cartData);

        $productData = (object) [
            'id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Producto A',
            'price' => 50.0,
            'quantity' => 10,
            'cartQuantity' => 0,
        ];
        $product = Product::fromDatabase($productData);

        $cart->addProduct($product, 5);

        $this->assertEquals(5, $cart->getProductQuantity(UuidVO::fromString($product->id)));

        $nonExistentProductId = UuidVO::fromString(Uuid::uuid4()->toString());
        $this->assertEquals(0, $cart->getProductQuantity($nonExistentProductId));
    }

    public function testCartIsEmptyAfterCheckout()
    {
        $cartData = [
            'id' => Uuid::uuid4(),
            'items' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $cart = Cart::create($cartData);

        $productData = (object) [
            'id' => Uuid::uuid4(),
            'name' => 'Gorra',
            'price' => 15.0,
            'quantity' => 10,
            'cartQuantity' => 0,
        ];
        $product = Product::fromDatabase($productData);

        $cart->addProduct($product, 2);
        $cart->checkout();

        $this->assertEmpty($cart->items);
    }

    /**
     * @throws DateMalformedStringException
     */
    public function testGetProduct()
    {
        $cartData = [
            'id' => Uuid::uuid4(),
            'items' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $cart = Cart::create($cartData);

        $productData = (object) [
            'id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Producto A',
            'price' => 50.0,
            'quantity' => 10,
            'cartQuantity' => 0,
        ];
        $product = Product::fromDatabase($productData);

        $cart->addProduct($product, 5);

        $retrievedProduct = $cart->getProduct(UuidVO::fromString($product->id));
        $this->assertNotNull($retrievedProduct);
        $this->assertEquals('Producto A', $retrievedProduct->name);

        $nonExistentProductId = UuidVO::fromString(Uuid::uuid4()->toString());
        $retrievedProduct = $cart->getProduct($nonExistentProductId);
        $this->assertNull($retrievedProduct);
    }

    /**
     * @throws DateMalformedStringException
     */
    public function testGetProductStock()
    {
        $cartData = [
            'id' => Uuid::uuid4(),
            'items' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $cart = Cart::create($cartData);

        $productData = (object) [
            'id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Producto B',
            'price' => 30.0,
            'quantity' => 20,
            'cartQuantity' => 0,
        ];
        $product = Product::fromDatabase($productData);

        $cart->addProduct($product, 5);

        $this->assertEquals(20, $cart->getProductStock(UuidVO::fromString($product->id)));

        $nonExistentProductId = UuidVO::fromString(Uuid::uuid4()->toString());
        $this->assertEquals(0, $cart->getProductStock($nonExistentProductId));
    }
}
