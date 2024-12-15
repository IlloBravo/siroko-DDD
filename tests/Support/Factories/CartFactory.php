<?php

namespace Tests\Support\Factories;

use App\Domain\Cart\Cart;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Support\Facades\DB;

class CartFactory
{
    private string $id;
    private array $items;

    public static function new(): self
    {
        return new self();
    }

    public function __construct()
    {
        $this->id = UuidVO::generate()->__toString();
        $this->items = [];
    }

    public function withItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function create(): Cart
    {
        DB::table('carts')->insert([
            'id' => $this->id,
            'items' => json_encode($this->items),
        ]);

        return Cart::fromDatabase((object)[
            'id' => $this->id,
            'items' => json_encode($this->items),
        ]);
    }
}