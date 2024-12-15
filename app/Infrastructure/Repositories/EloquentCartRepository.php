<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Cart\Cart;
use App\Domain\Cart\CartItem;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentCartRepository implements CartRepositoryInterface
{
    public function findByIdOrFail(UuidVO $id): Cart
    {
        $cartData = DB::table('carts')->where('id', (string) $id)->first();

        if (!$cartData) {
            throw new CartNotFoundException($id);
        }

        return Cart::fromDatabase($cartData);
    }

    public function findAll(): Collection
    {
        $cartsData = DB::table('carts')->get();

        return $cartsData->map(function ($cartData) {
            return Cart::fromDatabase($cartData);
        });
    }

    public function save(Cart $cart): void
    {
        $cartItemsData = $cart->getCartItems()->map(function (CartItem $item) {
            return [
                'id' => (string) $item->id,
                'cart_id' => (string) $item->cartId,
                'product_id' => (string) $item->productId,
                'quantity' => $item->quantity,
            ];
        })->toArray();

        DB::table('carts')->updateOrInsert(
            ['id' => (string) $cart->id],
            ['items' => json_encode($cartItemsData)]
        );
    }
}
