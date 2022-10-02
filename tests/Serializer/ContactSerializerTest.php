<?php declare(strict_types = 1);

namespace App\Serializer;

use App\Entity\Contact;
use App\Value\EmailAddress;
use App\Value\PhoneNumber;
use PHPUnit\Framework\TestCase;

class ContactSerializerTest extends TestCase
{

    public function testSerializeToJson(): void
    {
        $contact = new Contact(
            'Harry',
            'Šroubek',
            EmailAddress::fromString('harry@sroubek.com'),
            PhoneNumber::fromString('987 654 321'),
            'Pellentesque in sapien nunc. Pellentesque venenatis nibh ut porta dignissim.',
            'sroubek-harry'
        );

        self::assertSame(
            '{"name":"\u0160roubek Harry","notice":"Pellentesque in sapien nunc. Pellentesque venenatis nibh ut porta dignissim."}',
            ContactSerializer::serializeToJson($contact),
        );
    }

    public function testSerializeToJsonWithRequiredFieldsOnly(): void
    {
        $contact = new Contact(
            'Petrulie',
            'Svižná',
            EmailAddress::fromString('petrulie@svizna.com'),
            null,
            null,
            'svizna-petrulie'
        );

        self::assertSame(
            '{"name":"Svi\u017en\u00e1 Petrulie","notice":null}',
            ContactSerializer::serializeToJson($contact),
        );
    }

}
