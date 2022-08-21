<?php declare(strict_types = 1);

namespace App\Serializer;

use App\Entity\Contact;
use App\KernelTestCase;
use App\Value\EmailAddress;
use App\Value\PhoneNumber;

class ContactSerializerTest extends KernelTestCase
{

    public function testSerializeContactToJson(): void
    {
        $contactSerializer = $this->getServiceByType(ContactSerializer::class);

        $contact = new Contact(
            'Harry',
            'Šroubek',
            EmailAddress::fromString('harry@sroubek.com'),
            PhoneNumber::fromString('987 654 321'),
            'Pellentesque in sapien nunc. Pellentesque venenatis nibh ut porta dignissim.',
            'sroubek-harry',
        );

        self::assertSame(
            '{"name":"\u0160roubek Harry","notice":"Pellentesque in sapien nunc. Pellentesque venenatis nibh ut porta dignissim."}',
            $contactSerializer->serializeContactToJson($contact),
        );
    }

    public function testSerializeContactToJsonWithRequiredFieldsOnly(): void
    {
        $contactSerializer = $this->getServiceByType(ContactSerializer::class);

        $contact = new Contact(
            'Petrulie',
            'Svižná',
            EmailAddress::fromString('petrulie@svizna.com'),
            null,
            null,
            'svizna-petrulie',
        );

        self::assertSame(
            '{"name":"Svi\u017en\u00e1 Petrulie","notice":null}',
            $contactSerializer->serializeContactToJson($contact),
        );
    }

    public function testSerializeContactListPageToJson(): void
    {
        $contactSerializer = $this->getServiceByType(ContactSerializer::class);

        self::assertSame(
            '{"page":{"url":"\/","title":"Spr\u00e1vce kontakt\u016f","content":"<table class=\"table table-responsive\"><thead><tr><th>Jm\u00e9no<\/th><th>E-mail<\/th><th>Telefon<\/th><th><\/th><\/tr><\/thead><\/table>"}}', // phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
            $contactSerializer->serializeContactListPageToJson(
                '/',
                'Správce kontaktů',
                '<table class="table table-responsive"><thead><tr><th>Jméno</th><th>E-mail</th><th>Telefon</th><th></th></tr></thead></table>',
            ),
        );
    }

}
