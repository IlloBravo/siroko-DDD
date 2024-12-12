<?php

namespace Tests\Unit\Domain\Product;

use App\Domain\Product\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testCreateProduct()
    {
        $productData = (object)[
            'id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Producto A',
            'price' => 10.5,
            'quantity' => 100,
            'cartQuantity' => 0,
        ];

        $product = Product::fromDatabase($productData);

        $this->assertEquals('123e4567-e89b-12d3-a456-426614174001', $product->id->__toString());
        $this->assertEquals('Producto A', $product->name);
        $this->assertEquals(10.5, $product->price);
        $this->assertEquals(100, $product->stock);
        $this->assertEquals(0, $product->cartQuantity);
    }

    public function testDecreaseStock()
    {
        $productData = (object)[
            'id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Producto A',
            'price' => 10.5,
            'quantity' => 100,
            'cartQuantity' => 0,
        ];

        $product = Product::fromDatabase($productData);

        $product->decreaseStock(10);

        $this->assertEquals(90, $product->stock);
    }

    public function testIncreaseStock()
    {
        $productData = (object)[
            'id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Producto A',
            'price' => 10.5,
            'quantity' => 100,
            'cartQuantity' => 0,
        ];

        $product = Product::fromDatabase($productData);

        $product->increaseStock(15);

        $this->assertEquals(115, $product->stock);
    }

    public function testFromDatabase()
    {
        $productData = (object)[
            'id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Producto A',
            'price' => 10.5,
            'quantity' => 100,
            'cartQuantity' => 10,
        ];

        $product = Product::fromDatabase($productData);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals(10, $product->cartQuantity);
    }
}
