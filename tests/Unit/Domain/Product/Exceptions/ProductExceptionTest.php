<?php

namespace Tests\Unit\Domain\Product\Exceptions;

use App\Domain\Product\Exceptions\InsufficientStockException;
use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Lang;

class ProductExceptionTest extends TestCase
{
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $productData = (object) [
            'id' => UuidVO::generate()->__toString(),
            'name' => 'Laptop',
            'price' => 1500.00,
            'stock' => 10,
        ];

        $this->product = Product::fromDatabase($productData);
    }

    public function testInsufficientStockException(): void
    {
        Lang::shouldReceive('get')
            ->once()
            ->with('Product.insufficient_stock', [
                'name' => $this->product->name,
                'stock' => $this->product->stock,
            ])
            ->andReturn("No hay suficiente stock para el producto '{$this->product->name}'. Stock actual: {$this->product->stock}");

        $this->expectException(InsufficientStockException::class);
        $this->expectExceptionMessage("No hay suficiente stock para el producto '{$this->product->name}'. Stock actual: {$this->product->stock}");

        throw new InsufficientStockException($this->product->name, $this->product->stock);
    }

    public function testProductNotFoundException(): void
    {
        $productId = $this->product->id->__toString();

        Lang::shouldReceive('get')
            ->once()
            ->with('Product.product_not_found', ['id' => $productId])
            ->andReturn("Producto con ID $productId no encontrado");

        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage("Producto con ID $productId no encontrado");

        throw new ProductNotFoundException($productId);
    }
}