<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}