<?php declare(strict_types = 1);

namespace App\Fixtures;

use App\Entity\Contact;
use App\Value\EmailAddress;
use App\Value\PhoneNumber;
use Doctrine\ORM\EntityManager;

final class ContactDatabaseFixture extends Fixture
{

    public static Contact $contactMaxmilian;

    public const CONTACT_MAXMILIAN = 'contact-maxmilian';

    public static Contact $contactHarry;

    public const CONTACT_HARRY = 'contact-harry';

    public static Contact $contactHugo;

    public const CONTACT_HUGO = 'contact-hugo';

    public static Contact $contactGertruda;

    public const CONTACT_GERTRUDA = 'contact-gertruda';

    public function loadWithEntityManager(EntityManager $entityManager): void
    {
        self::$contactMaxmilian = new Contact(
            'Maxmilián',
            'Pumpička',
            EmailAddress::fromString('maxmilian@pumpicka.com'),
            PhoneNumber::fromString('123456789'),
            'Lorem ipsum dolor sit amet',
        );
        $this->addReference(self::CONTACT_MAXMILIAN, self::$contactMaxmilian);
        $entityManager->persist(self::$contactMaxmilian);

        self::$contactHarry = new Contact(
            'Harry',
            'Šroubek',
            EmailAddress::fromString('harry@sroubek.com'),
            PhoneNumber::fromString('456789123'),
            'Quisque facilisis, velit vel efficitur rutrum, nunc elit porta sem',
        );
        $this->addReference(self::CONTACT_HARRY, self::$contactHarry);
        $entityManager->persist(self::$contactHarry);

        self::$contactHugo = new Contact(
            'Hugo',
            'Ventil',
            EmailAddress::fromString('hugo@ventil.com'),
            null,
            null,
        );
        $this->addReference(self::CONTACT_HUGO, self::$contactHugo);
        $entityManager->persist(self::$contactHugo);

        self::$contactGertruda = new Contact(
            'Gertruda',
            'Pyšná',
            EmailAddress::fromString('gertruda@pysna.com'),
            null,
            null,
        );
        $this->addReference(self::CONTACT_GERTRUDA, self::$contactGertruda);
        $entityManager->persist(self::$contactGertruda);

        $entityManager->flush();
    }

}
