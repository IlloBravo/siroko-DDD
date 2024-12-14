<?php

namespace Tests\Unit\Domain\Cart;

use App\Domain\Cart\Cart;
use App\Domain\Cart\CartItem;
use App\Domain\Product\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    private Cart $cart;
    private CartItem $cartItem1;
    private CartItem $cartItem2;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $productData = (object) [
            'id' => UuidVO::generate()->__toString(),
            'name' => 'Bike',
            'price' => 1500.00,
            'stock' => 10,
        ];

        $cartId = UuidVO::generate()->__toString();
        $cartData = (object) [
            'id' => $cartId,
            'items' => json_encode([]),
        ];

        $this->cart = Cart::fromDatabase($cartData);

        $cartItemData1 = (object) [
            'id' => UuidVO::generate()->__toString(),
            'cart' => $cartData,
            'product' => $productData,
            'quantity' => 2,
        ];

        $cartItemData2 = (object) [
            'id' => UuidVO::generate()->__toString(),
            'cart' => $cartData,
            'product' => $productData,
            'quantity' => 4,
        ];

        $this->cartItem1 = CartItem::fromDatabase($cartItemData1);
        $this->cartItem2 = CartItem::fromDatabase($cartItemData2);

        $this->mockProductRepository($productData);
    }

    /**
     * @throws Exception
     */
    private function mockProductRepository(object $productData): void
    {
        $mockRepository = $this->createMock(ProductRepositoryInterface::class);

        $mockRepository
            ->method('findByIdOrFail')
            ->willReturn(Product::fromDatabase($productData));

        app()->instance(ProductRepositoryInterface::class, $mockRepository);
    }

    public function testCartCanBeCreatedFromDatabase(): void
    {
        $this->assertInstanceOf(Cart::class, $this->cart);
        $this->assertCount(0, $this->cart->getCartItems());
    }

    public function testCanAddCartItem(): void
    {
        $this->cart->addCartItem($this->cartItem1, 2);

        $this->assertCount(1, $this->cart->getCartItems());
        $this->assertEquals(2, $this->cart->getCartItems()->first()->quantity);
    }

    public function testCanUpdateCartItemQuantity(): void
    {
        $this->cart->addCartItem($this->cartItem1, 0);

        $this->cart->updateCartItemQuantity($this->cartItem1, 10);

        $this->assertEquals(10, $this->cart->getCartItems()->first()->quantity);
    }

    public function testCanRemoveCartItem(): void
    {
        $this->cart->addCartItem($this->cartItem1, 0);
        $this->cart->removeCartItem($this->cartItem1->id);

        $this->assertCount(0, $this->cart->getCartItems());
    }

    public function testCartCheckout(): void
    {
        $this->cart->addCartItem($this->cartItem1, 0);
        $this->cart->addCartItem($this->cartItem2, 0);

        $this->cart->checkout();

        $this->assertCount(0, $this->cart->getCartItems());
    }

    public function testCartTotal(): void
    {
        $this->cart->addCartItem($this->cartItem1, 0);
        $this->cart->addCartItem($this->cartItem2, 0);

        $this->assertEquals(3000.00, $this->cart->total());
    }

    public function testCartTotalWhenEmpty(): void
    {
        $this->assertEquals(0.00, $this->cart->total());
    }

    public function testCartCanBeInstantiatedWithNoItems(): void
    {
        $cartId = UuidVO::generate()->__toString();
        $cartData = (object) [
            'id' => $cartId,
            'items' => json_encode([]),
        ];

        $cart = Cart::fromDatabase($cartData);

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals($cartId, $cart->id->__toString());
        $this->assertCount(0, $cart->getCartItems());
    }

    public function testCartItemsAreProperlyInstantiatedFromValidDatabaseData(): void
    {
        $cartId = UuidVO::generate()->__toString();
        $productId1 = UuidVO::generate()->__toString();
        $productId2 = UuidVO::generate()->__toString();

        $cartData = (object) [
            'id' => $cartId,
            'items' => json_encode([
                [
                    'id' => UuidVO::generate()->__toString(),
                    'cartId' => $cartId,
                    'productId' => $productId1,
                    'quantity' => 3,
                ],
                [
                    'id' => UuidVO::generate()->__toString(),
                    'cartId' => $cartId,
                    'productId' => $productId2,
                    'quantity' => 4,
                ],
            ]),
        ];

        $cart = Cart::fromDatabase($cartData);

        $firstItem = $cart->getCartItems()->first();
        $this->assertEquals($cartId, $firstItem->cartId->__toString());
        $this->assertEquals($productId1, $firstItem->productId->__toString());
        $this->assertEquals(3, $firstItem->quantity);

        $secondItem = $cart->getCartItems()->last();
        $this->assertEquals($cartId, $secondItem->cartId->__toString());
        $this->assertEquals($productId2, $secondItem->productId->__toString());
        $this->assertEquals(4, $secondItem->quantity);
    }
}