<?php declare(strict_types = 1);

namespace App\Dto;

use App\Entity\Contact;
use App\Value\EmailAddress;
use App\Value\PhoneNumber;
use PHPUnit\Framework\TestCase;

final class ContactDtoTest extends TestCase
{

    public function testFromContact(): void
    {
        $contact = new Contact(
            'Hugo',
            'Pumpička',
            EmailAddress::fromString('hugo.pumpicka2@gmail.com'),
            PhoneNumber::fromString('777123456'),
            'Lorem ipsum dolor sit amet',
            'hugo-pumpicka',
        );

        $contactDto = ContactDto::fromContact($contact);

        self::assertSame('Hugo', $contactDto->getFirstname());
        self::assertSame('Pumpička', $contactDto->getLastname());
        self::assertSame('hugo.pumpicka2@gmail.com', $contactDto->getEmail());
        self::assertSame('777123456', $contactDto->getPhone());
        self::assertSame('Lorem ipsum dolor sit amet', $contactDto->getNotice());
        self::assertSame('hugo-pumpicka', $contactDto->getSlug());
    }

    public function testFromMinimalContact(): void
    {
        $contact = new Contact(
            'Hugo',
            'Pumpička',
            EmailAddress::fromString('hugo.pumpicka2@gmail.com'),
            null,
            null,
            'hugo-pumpicka',
        );

        $contactDto = ContactDto::fromContact($contact);

        self::assertSame('Hugo', $contactDto->getFirstname());
        self::assertSame('Pumpička', $contactDto->getLastname());
        self::assertSame('hugo.pumpicka2@gmail.com', $contactDto->getEmail());
        self::assertNull($contactDto->getPhone());
        self::assertNull($contactDto->getNotice());
        self::assertSame('hugo-pumpicka', $contactDto->getSlug());
    }

    public function testToArray(): void
    {
        $contactDto = new ContactDto(
            'Hugo',
            'Pumpička',
            'hugo.pumpicka2@gmail.com',
            '777123456',
            'Lorem ipsum dolor sit amet',
            'hugo-pumpicka',
        );

        $contactArray = $contactDto->toArray();

        self::assertSame('Hugo', $contactArray['firstname']);
        self::assertSame('Pumpička', $contactArray['lastname']);
        self::assertSame('hugo.pumpicka2@gmail.com', $contactArray['email']);
        self::assertSame('777123456', $contactArray['phone']);
        self::assertSame('Lorem ipsum dolor sit amet', $contactArray['notice']);
        self::assertSame('hugo-pumpicka', $contactArray['slug']);
    }

    public function testToArrayWithMinimalData(): void
    {
        $contactDto = new ContactDto(
            'Hugo',
            'Pumpička',
            'hugo.pumpicka2@gmail.com',
            null,
            null,
            'hugo-pumpicka',
        );

        $contactArray = $contactDto->toArray();

        self::assertSame('Hugo', $contactArray['firstname']);
        self::assertSame('Pumpička', $contactArray['lastname']);
        self::assertSame('hugo.pumpicka2@gmail.com', $contactArray['email']);
        self::assertNull($contactArray['phone']);
        self::assertNull($contactArray['notice']);
        self::assertSame('hugo-pumpicka', $contactArray['slug']);
    }

}
