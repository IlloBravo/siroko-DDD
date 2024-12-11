<?php

namespace App\Domain\Product\Exceptions;

use Illuminate\Support\Facades\Lang;
use RuntimeException;

class ProductNotFoundException extends RuntimeException
{
    public function __construct(string $productId)
    {
        $message = Lang::get('Product.product_not_found', ['id' => $productId]);
        parent::__construct($message);
    }
}