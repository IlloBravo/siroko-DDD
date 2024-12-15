<?php

namespace Tests\Support\Factories;

use App\Domain\Cart\CartItem;
use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Support\Facades\DB;

class CartItemFactory
{
    private string $id;
    private UuidVO $cartId;
    private UuidVO $productId;
    private int $quantity;

    public static function new(): self
    {
        return new self();
    }

    public function __construct()
    {
        $this->id = UuidVO::generate()->__toString();
        $this->cartId = CartFactory::new()->create()->id;
        $this->productId = ProductFactory::new()->create()->id;
        $this->quantity = 1;
    }

    public function withCartId(UuidVO $cartId): self
    {
        $this->cartId = $cartId;

        return $this;
    }

    public function withProductId(UuidVO $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function withQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function create(): CartItem
    {
        DB::table('cart_items')->insert([
            'id' => $this->id,
            'cart_id' => $this->cartId,
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
        ]);

        return CartItem::fromDatabase((object)[
            'id' => $this->id,
            'cart_id' => $this->cartId,
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
        ]);
    }
}