<?php declare(strict_types = 1);

namespace App\Model;

use App\Entity\Contact;
use App\Value\EmailAddress;
use App\Value\PhoneNumber;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final class ContactFacade
{

    private LoggerInterface $auditLogger;

    private EntityManagerInterface $entityManager;

    public function __construct(
        LoggerInterface $auditLogger,
        EntityManagerInterface $entityManager
    )
    {
        $this->auditLogger = $auditLogger;
        $this->entityManager = $entityManager;
    }

    public function create(
        string $firstname,
        string $lastname,
        string $email,
        ?string $phone,
        ?string $notice
    ): Contact
    {
        $contact = new Contact(
            $firstname,
            $lastname,
            EmailAddress::fromString($email),
            $phone !== null ? PhoneNumber::fromString($phone) : null,
            $notice
        );

        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        $this->auditLogger->info('Contact was created.', [
            'contactId' => $contact->getId(),
        ]);

        return $contact;
    }

    public function update(
        Contact $contact,
        string $firstname,
        string $lastname,
        string $email,
        ?string $phone,
        ?string $notice
    ): Contact
    {
        $contact->update(
            $firstname,
            $lastname,
            EmailAddress::fromString($email),
            $phone !== null ? PhoneNumber::fromString($phone) : null,
            $notice
        );

        $this->entityManager->flush();

        $this->auditLogger->info('Contact was updated.', [
            'contactId' => $contact->getId(),
        ]);

        return $contact;
    }

}
