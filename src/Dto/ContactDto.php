<?php declare(strict_types = 1);

namespace App\Dto;

use App\Entity\Contact;

// phpcs:disable PSR1.Files.SideEffects
final readonly class ContactDto
{

    public function __construct(
        private string $firstname,
        private string $lastname,
        private string $email,
        private ?string $phone,
        private ?string $notice,
        private string $slug,
    )
    {
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): ?string
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

    public static function fromContact(Contact $contact): self
    {
        return new self(
            $contact->getFirstname(),
            $contact->getLastname(),
            $contact->getEmail()->toString(),
            $contact->getPhone()?->toString(),
            $contact->getNotice(),
            $contact->getSlug(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'firstname' => $this->getFirstname(),
            'lastname' => $this->getLastname(),
            'email' => $this->getEmail(),
            'phone' => $this->getPhone(),
            'notice' => $this->getNotice(),
            'slug' => $this->getSlug(),
        ];
    }

}
