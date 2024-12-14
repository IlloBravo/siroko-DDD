<?php

namespace Tests\Support\Factories;

use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Support\Facades\DB;

class ProductFactory
{
    private string $id;
    private string $name;
    private float $price;
    private int $stock;

    public static function new(): self
    {
        return new self();
    }

    public function __construct()
    {
        $this->id = UuidVO::generate()->__toString();
        $this->name = 'Default Product';
        $this->price = 100.0;
        $this->stock = 10;
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function withPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function withStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function create(): Product
    {
        DB::table('products')->insert([
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
        ]);

        return Product::fromDatabase((object)[
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
        ]);
    }
}