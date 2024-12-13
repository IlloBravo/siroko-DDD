<?php

namespace App\Models;

use App\Domain\Cart\CartItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $table = 'carts';

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}