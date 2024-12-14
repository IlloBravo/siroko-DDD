<?php

namespace Tests\Unit\Domain\Cart;

use App\Domain\Cart\Cart;
use App\Domain\Cart\CartItem;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class CartItemTest extends TestCase
{
    private CartItem $cartItem;
    private Cart $cart;
    private Product $product;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $cartData = (object) [
            'id' => UuidVO::generate()->__toString(),
            'items' => json_encode([]),
        ];

        $productData = (object) [
            'id' => UuidVO::generate()->__toString(),
            'name' => 'Bike',
            'price' => 1500.00,
            'stock' => 10,
        ];

        $cartItemData = (object) [
            'id' => UuidVO::generate()->__toString(),
            'cart' => $cartData,
            'product' => $productData,
            'quantity' => 3,
        ];

        $this->cart = Cart::fromDatabase($cartData);
        $this->product = Product::fromDatabase($productData);
        $this->cartItem = CartItem::fromDatabase($cartItemData);

        $this->mockRepositories($cartData, $productData);
    }

    /**
     * @throws Exception
     */
    private function mockRepositories(object $cartData, object $productData): void
    {
        $mockProductRepository = $this->createMock(ProductRepositoryInterface::class);
        $mockProductRepository
            ->method('findByIdOrFail')
            ->willReturn(Product::fromDatabase($productData));

        app()->instance(ProductRepositoryInterface::class, $mockProductRepository);

        $mockCartRepository = $this->createMock(CartRepositoryInterface::class);
        $mockCartRepository
            ->method('findByIdOrFail')
            ->willReturn(Cart::fromDatabase($cartData));

        app()->instance(CartRepositoryInterface::class, $mockCartRepository);
    }

    public function testCartItemCanBeCreatedFromDatabase(): void
    {
        $this->assertInstanceOf(CartItem::class, $this->cartItem);
        $this->assertEquals($this->cart->id->__toString(), $this->cartItem->cartId->__toString());
        $this->assertEquals($this->product->id->__toString(), $this->cartItem->productId->__toString());
        $this->assertEquals(3, $this->cartItem->quantity);
    }

    public function testCartItemCanRetrieveProduct(): void
    {
        $product = $this->cartItem->product();
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($this->product->id->__toString(), $product->id->__toString());
    }

    public function testCartItemCanRetrieveCart(): void
    {
        $cart = $this->cartItem->cart();
        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals($this->cart->id->__toString(), $cart->id->__toString());
    }
}