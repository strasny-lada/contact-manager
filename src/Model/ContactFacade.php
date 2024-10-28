<?php declare(strict_types = 1);

namespace App\Model;

use App\Entity\Contact;
use App\Slugger\ContactSlugger;
use App\Value\EmailAddress;
use App\Value\PhoneNumber;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

// phpcs:disable PSR1.Files.SideEffects
final readonly class ContactFacade
{

    public const string PAGINATION_PAGE_HOLDER = 'pagination.page'; // phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase

    public function __construct(
        private ContactSlugger $slugger,
        private LoggerInterface $auditLogger,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function create(
        string $firstname,
        string $lastname,
        string $email,
        ?string $phone,
        ?string $notice,
    ): Contact
    {
        $contact = new Contact(
            $firstname,
            $lastname,
            EmailAddress::fromString($email),
            $phone !== null && $phone !== '' ? PhoneNumber::fromString($phone) : null,
            $notice !== null && $notice !== '' ? $notice : null,
            $this->slugger->slugify(sprintf(
                '%s %s',
                $lastname,
                $firstname,
            )),
        );

        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        $this->auditLogger->info('Contact was created.', [
            'contactId' => $contact->getId()->toString(),
        ]);

        return $contact;
    }

    public function update(
        Contact $contact,
        string $firstname,
        string $lastname,
        string $email,
        ?string $phone,
        ?string $notice,
    ): Contact
    {
        if (
            $firstname !== $contact->getFirstname() ||
            $lastname !== $contact->getLastname()
        ) {
            $slug = $this->slugger->slugify(sprintf(
                '%s %s',
                $lastname,
                $firstname,
            ));
        } else {
            $slug = $contact->getSlug();
        }

        $contact->update(
            $firstname,
            $lastname,
            EmailAddress::fromString($email),
            $phone !== null ? PhoneNumber::fromString($phone) : null,
            $notice,
            $slug,
        );

        $this->entityManager->flush();

        $this->auditLogger->info('Contact was updated.', [
            'contactId' => $contact->getId()->toString(),
        ]);

        return $contact;
    }

    public function delete(
        Contact $contact,
    ): void
    {
        $contactId = $contact->getId()->toString();

        $this->entityManager->remove($contact);
        $this->entityManager->flush();

        $this->auditLogger->info('Contact was deleted.', [
            'contactId' => $contactId,
        ]);
    }

}
