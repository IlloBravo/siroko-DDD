<?php

namespace Domain\Shared\Exceptions;

use App\Domain\Shared\Exceptions\InvalidQuantityException;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Lang;

class InvalidQuantityExceptionTest extends TestCase
{
    public function testInvalidUuidExceptionMessage()
    {
        $invalidUuid = 'invalid-uuid-string';

        Lang::shouldReceive('get')
            ->once()
            ->with('Shared.invalid_quantity', ['value' => $invalidUuid])
            ->andReturn('Cantidad incorrecta ' . $invalidUuid);

        $this->expectException(InvalidQuantityException::class);
        $this->expectExceptionMessage('Cantidad incorrecta ' . $invalidUuid);

        throw new InvalidQuantityException($invalidUuid);
    }
}
