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

    public function testUpdate(): void
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

        $contactFacade->update(
            $contact,
            'Kašpar',
            'Mráček',
            'kasparus@mrackulus.info',
            '+421 345 678 43 52',
            'Quisque facilisis, velit vel efficitur rutrum',
        );

        $contactUpdated = Fixture::findEntity($contact, $this->getEntityManager());

        self::assertSame('Kašpar', $contactUpdated->getFirstname());
        self::assertSame('Mráček', $contactUpdated->getLastname());
        self::assertSame('kasparus@mrackulus.info', $contactUpdated->getEmail()->toString());
        self::assertNotNull($contactUpdated->getPhone());
        self::assertSame('+421 345 678 43 52', $contactUpdated->getPhone()->toString());
        self::assertSame('Quisque facilisis, velit vel efficitur rutrum', $contactUpdated->getNotice());
        self::assertSame('Mráček Kašpar', $contactUpdated->getName());
        self::assertSame('mracek-kaspar', $contactUpdated->getSlug());
    }

    public function testUpdateWithRequiredFieldsOnly(): void
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

        $contactFacade->update(
            $contact,
            'Kašpar',
            'Mráček',
            'kasparus@mrackulus.info',
            null,
            null,
        );

        $contactUpdated = Fixture::findEntity($contact, $this->getEntityManager());

        self::assertSame('Kašpar', $contactUpdated->getFirstname());
        self::assertSame('Mráček', $contactUpdated->getLastname());
        self::assertSame('kasparus@mrackulus.info', $contactUpdated->getEmail()->toString());
        self::assertNull($contactUpdated->getPhone());
        self::assertNull($contactUpdated->getNotice());
        self::assertSame('Mráček Kašpar', $contactUpdated->getName());
        self::assertSame('mracek-kaspar', $contactUpdated->getSlug());
    }

}
