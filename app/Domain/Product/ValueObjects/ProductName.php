<?php

namespace App\Domain\Product\ValueObjects;

use InvalidArgumentException;

final class ProductName
{
    private string $value;

    public function __construct(string $value)
    {
        $this->ensureIsValidName($value);
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(ProductName $other): bool
    {
        return $this->value === $other->value;
    }

    private function ensureIsValidName(string $value): void
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Product name cannot be empty.');
        }

        if (strlen($value) > 255) {
            throw new InvalidArgumentException('Product name cannot exceed 255 characters.');
        }
    }
}
