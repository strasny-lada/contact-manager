<?php declare(strict_types = 1);

namespace App\Fixtures;

use App\Entity\Contact;
use App\Value\EmailAddress;
use App\Value\PhoneNumber;
use Doctrine\ORM\EntityManager;

final class ContactDatabaseFixture extends Fixture
{

    public static Contact $contactMaxmilian;

    public const string CONTACT_MAXMILIAN = 'contact-maxmilian'; // phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase

    public static Contact $contactHarry;

    public const string CONTACT_HARRY = 'contact-harry'; // phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase

    public static Contact $contactHugo;

    public const string CONTACT_HUGO = 'contact-hugo'; // phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase

    public static Contact $contactGertruda;

    public const string CONTACT_GERTRUDA = 'contact-gertruda'; // phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase

    public function loadWithEntityManager(EntityManager $entityManager): void
    {
        self::$contactMaxmilian = new Contact(
            'Maxmilián',
            'Pumpička',
            EmailAddress::fromString('maxmilian@pumpicka.com'),
            PhoneNumber::fromString('123456789'),
            'Lorem ipsum dolor sit amet',
            'pumpicka-maxmilian',
        );
        $this->addReference(self::CONTACT_MAXMILIAN, self::$contactMaxmilian);
        $entityManager->persist(self::$contactMaxmilian);

        self::$contactHarry = new Contact(
            'Harry',
            'Šroubek',
            EmailAddress::fromString('harry@sroubek.com'),
            PhoneNumber::fromString('456789123'),
            'Quisque facilisis, velit vel efficitur rutrum, nunc elit porta sem',
            'sroubek-harry',
        );
        $this->addReference(self::CONTACT_HARRY, self::$contactHarry);
        $entityManager->persist(self::$contactHarry);

        self::$contactHugo = new Contact(
            'Hugo',
            'Ventil',
            EmailAddress::fromString('hugo@ventil.com'),
            null,
            null,
            'ventil-hugo',
        );
        $this->addReference(self::CONTACT_HUGO, self::$contactHugo);
        $entityManager->persist(self::$contactHugo);

        self::$contactGertruda = new Contact(
            'Gertruda',
            'Pyšná',
            EmailAddress::fromString('gertruda@pysna.com'),
            null,
            null,
            'pysna-gertruda',
        );
        $this->addReference(self::CONTACT_GERTRUDA, self::$contactGertruda);
        $entityManager->persist(self::$contactGertruda);

        $entityManager->flush();
    }

}
