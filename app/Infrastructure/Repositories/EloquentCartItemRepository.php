<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Cart\CartItem;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartItemRepositoryInterface;
use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Support\Facades\DB;

class EloquentCartItemRepository implements CartItemRepositoryInterface
{
    public function findByIdOrFail(UuidVO $id): CartItem
    {
        $cartItemData = DB::table('cart_items')->where('id', (string) $id)->first();

        if (!$cartItemData) {
            throw new CartNotFoundException($id);
        }

        return CartItem::fromDatabase($cartItemData);
    }

    public function save(CartItem $cartItem): void
    {
        DB::table('cart_items')->updateOrInsert(
            ['id' => (string) $cartItem->id],
            [
                'cart_id' => (string) $cartItem->cart->id,
                'product_id' => (string) $cartItem->product->id,
                'quantity' => $cartItem->quantity,
            ]
        );
    }

    public function delete(UuidVO $id): void
    {
        DB::table('cart_items')->where('id', (string) $id)->delete();
    }
}