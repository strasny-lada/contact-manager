<?php declare(strict_types = 1);

namespace App\Entity;

use App\Value\EmailAddress;
use App\Value\PhoneNumber;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 */
class Contact
{

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /** @ORM\Column(type="string", enumType=ContactStatus::class) */
    private \App\Entity\ContactStatus $status;

    /** @ORM\Column(type="datetime_immutable") */
    private \DateTimeImmutable $createdAt;

    /** @ORM\Column(type="datetime_immutable", nullable=true) */
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        /**
         * @ORM\Column(type="string")
         */
        private string $firstname,
        /**
         * @ORM\Column(type="string")
         */
        private string $lastname,
        /**
         * @ORM\Column(type=EmailAddress::class)
         */
        private \App\Value\EmailAddress $email,
        /**
         * @ORM\Column(type=PhoneNumber::class, nullable=true)
         */
        private ?\App\Value\PhoneNumber $phone,
        /**
         * @ORM\Column(type="text", nullable=true)
         */
        private ?string $notice,
        /**
         * @ORM\Column(type="string")
         */
        private string $slug,
    )
    {
        $this->id = Uuid::uuid4();
        $this->status = ContactStatus::ACTIVE;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function update(
        string $firstname,
        string $lastname,
        EmailAddress $email,
        ?PhoneNumber $phone,
        ?string $notice,
        string $slug,
    ): void
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->phone = $phone;
        $this->notice = $notice;
        $this->slug = $slug;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getName(): string
    {
        return sprintf(
            '%s %s',
            $this->getLastname(),
            $this->getFirstname(),
        );
    }

    public function getEmail(): EmailAddress
    {
        return $this->email;
    }

    public function getPhone(): ?PhoneNumber
    {
        return $this->phone;
    }

    public function getNotice(): ?string
    {
        return $this->notice;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getStatus(): ContactStatus
    {
        return $this->status;
    }

}
