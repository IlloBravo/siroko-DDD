<?php

namespace Tests\Unit\Domain\Shared\Exceptions;

use App\Domain\Shared\Exceptions\InvalidUuidException;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Lang;

class InvalidUuidExceptionTest extends TestCase
{
    public function testInvalidUuidExceptionMessage()
    {
        $invalidUuid = 'invalid-uuid-string';

        Lang::shouldReceive('get')
            ->once()
            ->with('Uuid.invalid_uuid', ['value' => $invalidUuid])
            ->andReturn('El UUID proporcionado no es válido: ' . $invalidUuid);

        $this->expectException(InvalidUuidException::class);
        $this->expectExceptionMessage('El UUID proporcionado no es válido: ' . $invalidUuid);

        throw new InvalidUuidException($invalidUuid);
    }
}
