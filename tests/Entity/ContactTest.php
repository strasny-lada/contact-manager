<?php declare(strict_types = 1);

namespace App\Entity;

use App\Value\EmailAddress;
use App\Value\PhoneNumber;
use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{

    public function testCreation(): void
    {
        $contact = new Contact(
            'Maxmilián',
            'Pumpička',
            EmailAddress::fromString('maxmilian@pumpicka.com'),
            PhoneNumber::fromString('123456789'),
            'Lorem ipsum dolor sit amet',
            'pumpicka-maxmilian',
        );

        self::assertSame('Maxmilián', $contact->getFirstname());
        self::assertSame('Pumpička', $contact->getLastname());
        self::assertSame('maxmilian@pumpicka.com', $contact->getEmail()->toString());
        self::assertNotNull($contact->getPhone());
        self::assertSame('123456789', $contact->getPhone()->toString());
        self::assertSame('Lorem ipsum dolor sit amet', $contact->getNotice());

        self::assertSame('Pumpička Maxmilián', $contact->getName());
        self::assertSame('pumpicka-maxmilian', $contact->getSlug());
    }

    public function testCreationWithRequiredFieldsOnly(): void
    {
        $contact = new Contact(
            'Maxmilián',
            'Pumpička',
            EmailAddress::fromString('maxmilian@pumpicka.com'),
            null,
            null,
            'pumpicka-maxmilian',
        );

        self::assertSame('Maxmilián', $contact->getFirstname());
        self::assertSame('Pumpička', $contact->getLastname());
        self::assertSame('maxmilian@pumpicka.com', $contact->getEmail()->toString());
        self::assertNull($contact->getPhone());
        self::assertNull($contact->getNotice());

        self::assertSame('Pumpička Maxmilián', $contact->getName());
        self::assertSame('pumpicka-maxmilian', $contact->getSlug());
    }

    public function testUpdate(): void
    {
        $contact = new Contact(
            'Maxmilián',
            'Pumpička',
            EmailAddress::fromString('maxmilian@pumpicka.com'),
            PhoneNumber::fromString('123456789'),
            'Lorem ipsum dolor sit amet',
            'pumpicka-maxmilian',
        );

        $contact->update(
            'Harry',
            'Šroubek',
            EmailAddress::fromString('harry@sroubek.com'),
            PhoneNumber::fromString('987 654 321'),
            'Pellentesque in sapien nunc. Pellentesque venenatis nibh ut porta dignissim.',
            'sroubek-harry',
        );

        self::assertSame('Harry', $contact->getFirstname());
        self::assertSame('Šroubek', $contact->getLastname());
        self::assertSame('harry@sroubek.com', $contact->getEmail()->toString());
        self::assertNotNull($contact->getPhone());
        self::assertSame('987 654 321', $contact->getPhone()->toString());
        self::assertSame(
            'Pellentesque in sapien nunc. Pellentesque venenatis nibh ut porta dignissim.',
            $contact->getNotice()
        );

        self::assertSame('Šroubek Harry', $contact->getName());
        self::assertSame('sroubek-harry', $contact->getSlug());
    }

    public function testUpdateWithRequiredFieldsOnly(): void
    {
        $contact = new Contact(
            'Maxmilián',
            'Pumpička',
            EmailAddress::fromString('maxmilian@pumpicka.com'),
            PhoneNumber::fromString('123456789'),
            'Lorem ipsum dolor sit amet',
            'pumpicka-maxmilian',
        );

        $contact->update(
            'Harry',
            'Šroubek',
            EmailAddress::fromString('harry@sroubek.com'),
            null,
            null,
            'sroubek-harry',
        );

        self::assertSame('Harry', $contact->getFirstname());
        self::assertSame('Šroubek', $contact->getLastname());
        self::assertSame('harry@sroubek.com', $contact->getEmail()->toString());
        self::assertNull($contact->getPhone());
        self::assertNull($contact->getNotice());

        self::assertSame('Šroubek Harry', $contact->getName());
        self::assertSame('sroubek-harry', $contact->getSlug());
    }

}
