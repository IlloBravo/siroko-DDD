<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Cart\CartItem;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartItemRepositoryInterface;
use App\Domain\Product\Product;
use App\Domain\Shared\Exceptions\InvalidQuantityException;
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

    public function create(UuidVO $cartId, UuidVO $productId, int $quantity): CartItem
    {
        if ($quantity <= 0) {
            throw new InvalidQuantityException($quantity);
        }

        $cartItem = new CartItem(UuidVO::generate(), $cartId, $productId, $quantity);

        DB::table('cart_items')->insert([
            'id' => (string) $cartItem->id,
            'cart_id' => (string) $cartId,
            'product_id' => (string) $productId,
            'quantity' => $quantity,
        ]);

        return $cartItem;
    }

    public function updateQuantity(UuidVO $cartItemId, int $newQuantity): void
    {
        if ($newQuantity <= 0) {
            throw new InvalidQuantityException($newQuantity);
        }

        DB::table('cart_items')
            ->where('id', (string) $cartItemId)
            ->update(['quantity' => $newQuantity]);
    }

    public function save(CartItem $cartItem): void
    {
        DB::table('cart_items')->updateOrInsert(
            ['id' => (string) $cartItem->id],
            [
                'cart_id' => (string) $cartItem->cartId,
                'product_id' => (string) $cartItem->productId,
                'quantity' => $cartItem->quantity,
            ]
        );
    }

    public function delete(UuidVO $id): void
    {
        DB::table('cart_items')->where('id', (string) $id)->delete();
    }
}