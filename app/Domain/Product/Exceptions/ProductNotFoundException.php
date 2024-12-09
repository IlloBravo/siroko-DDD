<?php

namespace App\Domain\Product\Exceptions;

use RuntimeException;

class ProductNotFoundException extends RuntimeException
{
    public function __construct(string $productId)
    {
        parent::__construct("Product with ID {$productId} not found.");
    }
}
