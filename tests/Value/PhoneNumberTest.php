<?php declare(strict_types = 1);

namespace App\Value;

use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{

    public function testEmptyPhoneNumber(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionMessage('Invalid phone number ""');

        PhoneNumber::fromString('');
    }

    public function testValidPhoneNumber(): void
    {
        $phone = PhoneNumber::fromString('+123456789');

        self::assertSame('+123456789', $phone->toString());
        self::assertSame('+123456789', (string) $phone);
    }

    public function testValidPhoneNumberWithSpacesAround(): void
    {
        $phone = PhoneNumber::fromString(' +123456789 ');

        self::assertSame('+123456789', $phone->toString());
        self::assertSame('+123456789', (string) $phone);
    }

}
