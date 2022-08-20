<?php declare(strict_types = 1);

namespace App\Value;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class EmailAddressTest extends TestCase
{

    public function testFromString(): void
    {
        $emailAddress = EmailAddress::fromString('maxmilian@pumpicka.com');

        Assert::assertSame('maxmilian@pumpicka.com', $emailAddress->toString());
    }

    public function testEmailAddressIsLowerCased(): void
    {
        $emailAddress = EmailAddress::fromString('Harry@Sroubek.com');

        Assert::assertSame('harry@sroubek.com', $emailAddress->toString());
    }

    public function testInvalidEmailAddress(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionMessage('Invalid email address "foo-"');

        EmailAddress::fromString('foo-');
    }

    public function testEmailValidation(): void
    {
        Assert::assertTrue(EmailAddress::isValidEmailAddress('hugo@ventil.com'));
        Assert::assertTrue(EmailAddress::isValidEmailAddress('Hugo@Ventil.COM'));
        Assert::assertFalse(EmailAddress::isValidEmailAddress('hugo-'));
    }

    public function testGetDomain(): void
    {
        $domain = EmailAddress::fromString('gertruda@pysna.com')->getDomain();

        Assert::assertSame('pysna.com', $domain);
    }

}
