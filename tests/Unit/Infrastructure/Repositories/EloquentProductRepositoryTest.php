<?php

namespace Tests\Unit\Infrastructure\Repositories;

use App\Domain\Product\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Shared\ValueObjects\UuidVO;
use App\Infrastructure\Repositories\EloquentProductRepository;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EloquentProductRepositoryTest extends TestCase
{
    public function testFindByIdOrFailReturnsProduct(): void
    {
        $productId = UuidVO::generate();
        $productData = (object)[
            'id' => $productId,
            'name' => 'Product A',
            'price' => 10.5,
            'quantity' => 100,
            'cartQuantity' => 0,
        ];

        $product = Product::fromDatabase($productData);

        DB::table('products')->insert([
            'id' => (string) $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $product->stock,
            'cartQuantity' => $product->cartQuantity,
            'updated_at' => now(),
        ]);

        $repository = new EloquentProductRepository();
        $retrievedProduct = $repository->findByIdOrFail($product->id);

        $this->assertInstanceOf(Product::class, $retrievedProduct);
        $this->assertEquals($product->id, $retrievedProduct->id);
        $this->assertEquals('Product A', $retrievedProduct->name);
    }

    public function testFindByIdOrFailThrowsExceptionWhenProductNotFound(): void
    {
        $productId = UuidVO::generate();

        $this->expectException(ProductNotFoundException::class);

        $repository = new EloquentProductRepository();
        $repository->findByIdOrFail($productId);
    }

    public function testUpdateStockSuccessfullyDecrementsQuantity(): void
    {
        $productId = UuidVO::generate();
        $initialQuantity = 10;
        $quantityToDecrement = 3;

        DB::table('products')->insert([
            'id' => (string) $productId,
            'name' => 'Product A',
            'price' => 100.0,
            'quantity' => $initialQuantity,
            'cartQuantity' => 2,
            'updated_at' => now(),
        ]);

        $repository = new EloquentProductRepository();
        $repository->updateStock($productId, $quantityToDecrement);

        $productData = DB::table('products')->where('id', (string) $productId)->first();
        $this->assertEquals($initialQuantity - $quantityToDecrement, $productData->quantity);
    }

    public function testIncreaseStockSuccessfullyIncrementsQuantity(): void
    {
        $productId = UuidVO::generate();
        $initialQuantity = 10;
        $quantityToIncrement = 5;

        DB::table('products')->insert([
            'id' => (string) $productId,
            'name' => 'Product A',
            'price' => 100.0,
            'quantity' => $initialQuantity,
            'cartQuantity' => 2,
            'updated_at' => now(),
        ]);

        $repository = new EloquentProductRepository();
        $repository->increaseStock($productId, $quantityToIncrement);

        $productData = DB::table('products')->where('id', (string) $productId)->first();
        $this->assertEquals($initialQuantity + $quantityToIncrement, $productData->quantity);
    }

    public function testSaveSuccessfullyUpdatesProduct(): void
    {
        $productId = UuidVO::generate();
        $initialQuantity = 10;

        $product = new Product(
            $productId,
            'Product A',
            100.0,
            $initialQuantity,
            2
        );

        DB::table('products')->insert([
            'id' => (string) $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $product->stock,
            'cartQuantity' => $product->cartQuantity,
            'updated_at' => now(),
        ]);

        $product->name = 'Updated Product A';
        $product->price = 120.0;
        $repository = new EloquentProductRepository();
        $repository->save($product);

        $updatedProductData = DB::table('products')->where('id', (string) $product->id)->first();
        $this->assertEquals('Updated Product A', $updatedProductData->name);
        $this->assertEquals(120.0, $updatedProductData->price);
    }
}
