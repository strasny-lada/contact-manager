<?php declare(strict_types = 1);

namespace App\Model;

use App\Fixtures\Fixture;
use App\WebTestCase;

class ContactFacadeTest extends WebTestCase
{

    public function testCreate(): void
    {
        $contactFacade = $this->getServiceByType(ContactFacade::class);

        $contact = $contactFacade->create(
            'Petrulie',
            'Svižná',
            'petrulie@svizna.com',
            '1234556789',
            'Lorem ipsum dolor sit amet',
        );

        $contactCreated = Fixture::findEntity($contact, $this->getEntityManager());

        self::assertSame('Petrulie', $contactCreated->getFirstname());
        self::assertSame('Svižná', $contactCreated->getLastname());
        self::assertSame('petrulie@svizna.com', $contactCreated->getEmail()->toString());
        self::assertNotNull($contactCreated->getPhone());
        self::assertSame('1234556789', $contactCreated->getPhone()->toString());
        self::assertSame('Lorem ipsum dolor sit amet', $contactCreated->getNotice());
        self::assertSame('Svižná Petrulie', $contactCreated->getName());
        self::assertSame('svizna-petrulie', $contactCreated->getSlug());
    }

    public function testCreateWithRequiredFieldsOnly(): void
    {
        $contactFacade = $this->getServiceByType(ContactFacade::class);

        $contact = $contactFacade->create(
            'Petrulie',
            'Svižná',
            'petrulie@svizna.com',
            null,
            null,
        );

        $contactCreated = Fixture::findEntity($contact, $this->getEntityManager());

        self::assertSame('Petrulie', $contactCreated->getFirstname());
        self::assertSame('Svižná', $contactCreated->getLastname());
        self::assertSame('petrulie@svizna.com', $contactCreated->getEmail()->toString());
        self::assertNull($contactCreated->getPhone());
        self::assertNull($contactCreated->getNotice());
        self::assertSame('Svižná Petrulie', $contactCreated->getName());
        self::assertSame('svizna-petrulie', $contactCreated->getSlug());
    }

}
