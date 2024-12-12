<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Cart\Cart;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use DateMalformedStringException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentCartRepository implements CartRepositoryInterface
{
    /**
     * @throws DateMalformedStringException
     */
    public function findByIdOrFail(UuidVO $id): Cart
    {
        $uuid = UuidVO::fromString($id);

        $cartData = DB::table('carts')->where('id', $uuid)->first();

        if (!$cartData) {
            throw new CartNotFoundException($id);
        }

        return Cart::fromDatabase($cartData);
    }

    public function findAll(): Collection
    {
        $cartsData = DB::table('carts')->get();

        return $cartsData->map(fn($cartData) => Cart::fromDatabase($cartData));
    }

    public function save(Cart $cart): void
    {
        DB::table('carts')->updateOrInsert(
            ['id' => (string) $cart->id],
            [
                'items' => json_encode($cart->products->map(function (Product $item) {
                    return [
                        'id' => (string) $item->id,
                        'name' => $item->name,
                        'price' => $item->price,
                        'stock' => $item->stock,
                        'cartQuantity' => $item->cartQuantity
                    ];
                })->values()->all()),
                'created_at' => $cart->createdAt,
                'updated_at' => $cart->updatedAt,
            ]
        );
    }

    public function delete(UuidVO $id): void
    {
        DB::table('carts')->where('id', $id)->delete();
    }
}
