<?php

namespace Tests\Unit\Domain\Cart;

use App\Domain\Cart\Cart;
use App\Domain\Cart\CartItem;
use App\Domain\Product\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

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
            'cart_id' => $cartData->id,
            'product_id' => $productData->id,
            'quantity' => 2,
        ];

        $cartItemData2 = (object) [
            'id' => UuidVO::generate()->__toString(),
            'cart_id' => $cartData->id,
            'product_id' => $productData->id,
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

        $product_id_1 = UuidVO::generate()->__toString();
        DB::table('products')->insert([
            'id' => $product_id_1,
            'name' => 'Bike',
            'price' => 1500.00,
            'stock' => 10,
        ]);

        $cart_id = UuidVO::generate()->__toString();
        DB::table('carts')->insert([
            'id' => $cart_id,
            'items' => json_encode([]),
        ]);

        $cart_item_id = UuidVO::generate()->__toString();
        DB::table('cart_items')->insert([
            'id' => $cart_item_id,
            'cart_id' => $cart_id,
            'product_id' => $product_id_1,
            'quantity' => 3,
        ]);

        DB::table('carts')
            ->where('id', $cart_id)
            ->update([
                'items' => json_encode([
                    [
                        'id' => $cart_item_id,
                        'cart_id' => $cart_id,
                        'product_id' => $product_id_1,
                        'quantity' => 3,
                    ]
                ]),
            ]);

        $cartRow = DB::table('carts')->where('id', $cart_id)->first();

        $cart = Cart::fromDatabase($cartRow);
        $firstItem = $cart->getCartItems()->first();
        $this->assertEquals($cart_id, $firstItem->cartId->__toString());
        $this->assertEquals($product_id_1, $firstItem->productId->__toString());
        $this->assertEquals(3, $firstItem->quantity);
    }
}