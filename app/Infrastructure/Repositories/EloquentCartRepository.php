<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Cart\Cart;
use App\Domain\Cart\CartItem;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Product;
use DateMalformedStringException;
use Illuminate\Support\Facades\DB;

class EloquentCartRepository implements CartRepositoryInterface
{
    public function save(Cart $cart): void
    {
        $items = $cart->items->map(function (CartItem $item) {
            return [
                'product' => [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                ],
            ];
        })->toArray();

        DB::table('carts')->updateOrInsert(
            ['id' => $cart->id],
            [
                'items' => json_encode($items),
                'created_at' => $cart->createdAt->format('Y-m-d H:i:s'),
                'updated_at' => $cart->updatedAt->format('Y-m-d H:i:s'),
            ]
        );
    }

    /**
     * @throws DateMalformedStringException
     */
    public function findByIdOrFail(string $id): Cart
    {
        $cartData = DB::table('carts')->where('id', $id)->first();

        if (!$cartData) {
            throw new CartNotFoundException($id);
        }

        $items = collect(json_decode($cartData->items, true))->map(function (array $item): CartItem {
            return CartItem::fromProduct(
                Product::fromArray($item['product']),
                $item['product']['quantity']
            );
        });

        return Cart::fromArray(
            $cartData->id,
            $items,
            $cartData->created_at,
            $cartData->updated_at
        );
    }

    public function delete(string $id): void
    {
        DB::table('carts')->where('id', $id)->delete();
    }
}
