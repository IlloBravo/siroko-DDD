<?php

namespace Tests\Unit\Domain\Product;

use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private Product $product;
    private UuidVO $productId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productId = UuidVO::generate();

        $productData = (object) [
            'id' => $this->productId->__toString(),
            'name' => 'Laptop',
            'price' => 1500.00,
            'stock' => 10,
        ];

        $this->product = Product::fromDatabase($productData);
    }

    public function testProductCanBeCreatedFromDatabase(): void
    {
        $this->assertInstanceOf(Product::class, $this->product);
        $this->assertEquals($this->productId->__toString(), $this->product->id->__toString());
        $this->assertEquals('Laptop', $this->product->name);
        $this->assertEquals(1500.00, $this->product->price);
        $this->assertEquals(10, $this->product->stock);
    }

    public function testProductHasSufficientStock(): void
    {
        $this->assertTrue($this->product->hasSufficientStock(5));
        $this->assertFalse($this->product->hasSufficientStock(15));
    }

    public function testDecreaseStock(): void
    {
        $this->product->decreaseStock(3);
        $this->assertEquals(7, $this->product->stock);
    }

    public function testIncreaseStock(): void
    {
        $this->product->increaseStock(5);
        $this->assertEquals(15, $this->product->stock);
    }

    public function testDecreaseStockToZero(): void
    {
        $this->product->decreaseStock(10);
        $this->assertEquals(0, $this->product->stock);
    }
}