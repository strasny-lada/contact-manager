<?php declare(strict_types = 1);

namespace App\Serializer;

use App\Dto\ContactDto;
use App\Dto\ContactListPageDto;
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

        $contactHarry = new Contact(
            'Harry',
            'Šroubek',
            EmailAddress::fromString('harry@sroubek.com'),
            PhoneNumber::fromString('987 654 321'),
            'Pellentesque in sapien nunc. Pellentesque venenatis nibh ut porta dignissim.',
            'sroubek-harry',
        );

        $contactPetrulie = new Contact(
            'Petrulie',
            'Svižná',
            EmailAddress::fromString('petrulie@svizna.com'),
            null,
            null,
            'svizna-petrulie',
        );

        $contactListPageDto = new ContactListPageDto(
            1,
            'Správce kontaktů',
            '/',
            [
                ContactDto::fromContact($contactHarry),
                ContactDto::fromContact($contactPetrulie),
            ],
            [
                'current' => 1,
                'last' => 2,
            ],
        );

        self::assertSame(
            '{"page":{"number":1,"title":"Spr\u00e1vce kontakt\u016f","url":"\/","items":[{"firstname":"Harry","lastname":"\u0160roubek","email":"harry@sroubek.com","phone":"987 654 321","notice":"Pellentesque in sapien nunc. Pellentesque venenatis nibh ut porta dignissim.","slug":"sroubek-harry"},{"firstname":"Petrulie","lastname":"Svi\u017en\u00e1","email":"petrulie@svizna.com","phone":null,"notice":null,"slug":"svizna-petrulie"}],"paginationData":{"current":1,"last":2}},"texts":{"lorem.ipsum":"Lorem ipsum","dolor.sit.amet":"Dolor sit amet"},"urls":{"lorem_ipsum":"\/lorem-ipsum"}}', // phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
            $contactSerializer->serializeContactListPageToJson(
                $contactListPageDto,
                [
                    'lorem.ipsum' => 'Lorem ipsum',
                    'dolor.sit.amet' => 'Dolor sit amet',
                ],
                [
                    'lorem_ipsum' => '/lorem-ipsum',
                ],
            ),
        );
    }

}
