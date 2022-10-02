<?php declare(strict_types = 1);

namespace App\Model;

use App\Entity\Contact;
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

    public function testSlugCanBeUpdated(): void
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
        self::assertSame('svizna-petrulie', $contactCreated->getSlug());

        $contactFacade->update(
            $contact,
            'Šárka',
            'Černochová',
            'sarka@cernochova.info',
            null,
            null,
        );

        $contactUpdated = Fixture::findEntity($contact, $this->getEntityManager());
        self::assertSame('cernochova-sarka', $contactUpdated->getSlug());
    }

    public function testSlugCanBeUpdatedWithNumberSuffix(): void
    {
        $contactFacade = $this->getServiceByType(ContactFacade::class);

        $contactSvizna1 = $contactFacade->create(
            'Petrulie',
            'Svižná',
            'petrulie@svizna.com',
            '1234556789',
            'Lorem ipsum dolor sit amet',
        );

        $contactSvizna1Created = Fixture::findEntity($contactSvizna1, $this->getEntityManager());
        self::assertSame('svizna-petrulie', $contactSvizna1Created->getSlug());

        $contactSvizna2 = $contactFacade->create(
            'Petra',
            'Svižná',
            'petra.svizna@gmail.com',
            null,
            null,
        );

        $contactSvizna2Created = Fixture::findEntity($contactSvizna2, $this->getEntityManager());
        self::assertSame('svizna-petra', $contactSvizna2Created->getSlug());

        $contactFacade->update(
            $contactSvizna1,
            'Petra',
            'Svižná',
            'petra@svizna.com',
            '1234556789',
            'Lorem ipsum dolor sit amet',
        );

        $contactSvizna1Updated = Fixture::findEntity($contactSvizna1, $this->getEntityManager());
        self::assertSame('svizna-petra-2', $contactSvizna1Updated->getSlug());
    }

    public function testSlugIsNotUpdatedWhenNameIsNotChanged(): void
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
        self::assertSame('svizna-petrulie', $contactCreated->getSlug());

        $contactFacade->update(
            $contact,
            'Petrulie',
            'Svižná',
            'petrulie@svizna.cz', // updated email
            '1234556789',
            'Lorem ipsum dolor sit amet',
        );

        $contactUpdated = Fixture::findEntity($contact, $this->getEntityManager());
        self::assertSame('petrulie@svizna.cz', $contactUpdated->getEmail()->toString());
        self::assertSame('svizna-petrulie', $contactUpdated->getSlug()); // slug is not changed
    }

    public function testDelete(): void
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

        $contactId = $contactCreated->getId()->toString();

        $contactFacade->delete($contact);

        $contactDeleted = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('contact')
            ->from(Contact::class, 'contact')
            ->andWhere('contact.id = :id')->setParameter('id', $contactId)
            ->getQuery()
            ->getOneOrNullResult();
        self::assertNull($contactDeleted);
    }

}
