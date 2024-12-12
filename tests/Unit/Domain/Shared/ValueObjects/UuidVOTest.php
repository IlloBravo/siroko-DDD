<?php

namespace Tests\Unit\Domain\Shared\ValueObjects;

use App\Domain\Shared\ValueObjects\UuidVO;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Lang;
use App\Domain\Shared\Exceptions\InvalidUuidException;

class UuidVOTest extends TestCase
{
    public function testValidUuid()
    {
        $validUuid = '123e4567-e89b-12d3-a456-426614174001';

        $uuidVO = new UuidVO($validUuid);

        $this->assertEquals($validUuid, (string) $uuidVO);
    }

    public function testInvalidUuidThrowsException()
    {
        $invalidUuid = 'invalid-uuid-string';

        Lang::shouldReceive('get')
            ->once()
            ->with('Uuid.invalid_uuid', ['value' => $invalidUuid])
            ->andReturn('El UUID proporcionado no es válido: ' . $invalidUuid);

        $this->expectException(InvalidUuidException::class);
        $this->expectExceptionMessage('El UUID proporcionado no es válido: ' . $invalidUuid);

        new UuidVO($invalidUuid);
    }

    public function testEqualsMethod()
    {
        $uuid1 = new UuidVO('123e4567-e89b-12d3-a456-426614174001');
        $uuid2 = new UuidVO('123e4567-e89b-12d3-a456-426614174001');
        $uuid3 = new UuidVO('123e4567-e89b-12d3-a456-426614174002');

        $this->assertTrue($uuid1->equals($uuid2));

        $this->assertFalse($uuid1->equals($uuid3));
    }

    public function testFromString()
    {
        $uuidString = '123e4567-e89b-12d3-a456-426614174001';
        $uuidVO = UuidVO::fromString($uuidString);

        $this->assertEquals($uuidString, (string) $uuidVO);
    }
}
