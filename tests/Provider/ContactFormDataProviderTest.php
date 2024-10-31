<?php declare(strict_types = 1);

namespace App\Provider;

use App\Entity\Contact;
use App\Value\EmailAddress;
use App\Value\PhoneNumber;
use App\WebTestCase;

final class ContactFormDataProviderTest extends WebTestCase
{

    public function testProvideSerializedContactFormData(): void
    {
        $contactFormDataProvider = $this->getServiceByType(ContactFormDataProvider::class);

        $contact = new Contact(
            'Hugo',
            'Pumpička',
            EmailAddress::fromString('hugo.pumpicka2@gmail.com'),
            PhoneNumber::fromString('777123456'),
            'Lorem ipsum dolor sit amet',
            'hugo-pumpicka',
        );

        $serializedContactFormData = $contactFormDataProvider->provideSerializedContactFormData(
            $contact,
        );

        self::assertSame(
            '{"contact":{"firstname":"Hugo","lastname":"Pumpi\u010dka","email":"hugo.pumpicka2@gmail.com","phone":"777123456","notice":"Lorem ipsum dolor sit amet","slug":"hugo-pumpicka"}}', // phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
            $serializedContactFormData,
        );
    }

    public function testProvideContactFormData(): void
    {
        $contactFormDataProvider = $this->getServiceByType(ContactFormDataProvider::class);

        $contact = new Contact(
            'Hugo',
            'Pumpička',
            EmailAddress::fromString('hugo.pumpicka2@gmail.com'),
            PhoneNumber::fromString('777123456'),
            'Lorem ipsum dolor sit amet',
            'hugo-pumpicka',
        );

        $contactFormData = $contactFormDataProvider->provideContactFormData(
            $contact,
        );

        self::assertNotNull($contactFormData->getContactDto());
        self::assertSame('Hugo', $contactFormData->getContactDto()->getFirstname());
        self::assertSame('Pumpička', $contactFormData->getContactDto()->getLastname());
        self::assertSame('hugo.pumpicka2@gmail.com', $contactFormData->getContactDto()->getEmail());
        self::assertSame('777123456', $contactFormData->getContactDto()->getPhone());
        self::assertSame('Lorem ipsum dolor sit amet', $contactFormData->getContactDto()->getNotice());
        self::assertSame('hugo-pumpicka', $contactFormData->getContactDto()->getSlug());

        self::assertCount(7, $contactFormData->getTexts());
    }

    public function testProvideContactFormDataWithNullableContact(): void
    {
        $contactFormDataProvider = $this->getServiceByType(ContactFormDataProvider::class);

        $contactFormData = $contactFormDataProvider->provideContactFormData(
            null,
        );

        self::assertNull($contactFormData->getContactDto());
        self::assertCount(7, $contactFormData->getTexts());
    }

}
