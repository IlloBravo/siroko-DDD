<?php

namespace Tests\Unit\Infrastructure\Repositories;

use App\Domain\Product\Product;
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
            'stock' => 100,
        ];

        $product = Product::fromDatabase($productData);

        DB::table('products')->insert([
            'id' => (string) $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->price,
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

    public function testFindAllReturnsCollection(): void
    {
        $productId = UuidVO::generate();
        $productData = (object)[
            'id' => $productId,
            'name' => 'Product A',
            'price' => 10.5,
            'stock' => 100,
        ];

        $product = Product::fromDatabase($productData);

        $product2Id = UuidVO::generate();
        $product2Data = (object)[
            'id' => $product2Id,
            'name' => 'Product B',
            'price' => 15,
            'stock' => 10,
        ];

        $product2 = Product::fromDatabase($product2Data);

        DB::table('products')->insert([
            'id' => (string) $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->price,
        ]);

        DB::table('products')->insert([
            'id' => (string) $product2->id,
            'name' => $product2->name,
            'price' => $product2->price,
            'stock' => $product2->price,
        ]);

        $repository = new EloquentProductRepository();
        $retrievedProduct = $repository->findAll();

        foreach ($retrievedProduct as $productFromCollection) {
            $this->assertInstanceOf(Product::class, $productFromCollection);
        }
    }

    public function testSaveSuccessfullyUpdatesProduct(): void
    {
        $productId = UuidVO::generate();
        $initialQuantity = 10;

        $product = new Product(
            $productId,
            'Product A',
            100.0,
            $initialQuantity
        );

        DB::table('products')->insert([
            'id' => (string) $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
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
