<?php declare(strict_types = 1);

// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint

namespace App\Entity;

use App\Value\EmailAddress;
use App\Value\PhoneNumber;
use Doctrine\ORM\Mapping as ORM;
use SymfonyBundles\Slugify\Slugify;

/**
 * @ORM\Entity()
 */
class Contact
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $firstname;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $lastname;

    /**
     * @ORM\Column(type=PhoneNumber::class, nullable=true)
     * @var \App\Value\PhoneNumber|null
     */
    private $phone;

    /**
     * @ORM\Column(type=EmailAddress::class)
     * @var \App\Value\EmailAddress
     */
    private $email;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string|null
     */
    private $notice;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @var \DateTimeImmutable|null
     */
    private $updatedAt;

    public function __construct(
        string $firstname,
        string $lastname,
        EmailAddress $email,
        ?PhoneNumber $phone,
        ?string $notice
    )
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->phone = $phone;
        $this->notice = $notice;
        $this->slug = Slugify::create($this->getName(), '');
        $this->createdAt = new \DateTimeImmutable();
    }

    public function update(
        string $firstname,
        string $lastname,
        EmailAddress $email,
        ?PhoneNumber $phone,
        ?string $notice
    ): void
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->phone = $phone;
        $this->notice = $notice;
        $this->slug = Slugify::create($this->getName(), '');
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): int
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

}
