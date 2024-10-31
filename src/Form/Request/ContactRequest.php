<?php declare(strict_types = 1);

// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint

namespace App\Form\Request;

use App\Entity\Contact;
use Symfony\Component\Validator\Constraints as Assert;

final class ContactRequest
{

    /**
     * @Assert\NotBlank()
     * @phpstan-ignore-next-line uninitialized property
     */
    public string $firstname;

    /**
     * @Assert\NotBlank()
     * @phpstan-ignore-next-line uninitialized property
     */
    public string $lastname;

    /**
     * @Assert\NotBlank()
     * @Assert\Email(
     *     mode="strict"
     * )
     * @phpstan-ignore-next-line uninitialized property
     */
    public string $email;

    public ?string $phone = null;

    public ?string $notice = null;

    public static function from(Contact $contact): self
    {
        $request = new self();

        $request->firstname = $contact->getFirstname();
        $request->lastname = $contact->getLastname();
        $request->email = $contact->getEmail()->toString();
        $request->phone = $contact->getPhone()?->toString();
        $request->notice = $contact->getNotice();

        return $request;
    }

    /**
     * @param array<string, mixed> $formData
     */
    public static function fromArray(array $formData): self
    {
        $requestObject = new self();

        $requestObject->firstname = $formData['firstname'];
        $requestObject->lastname = $formData['lastname'];
        $requestObject->email = $formData['email'];

        $phone = $formData['phone'] ?? null;
        if ($phone !== null) {
            $requestObject->phone = $phone;
        }

        $notice = $formData['notice'] ?? null;
        if ($notice !== null) {
            $requestObject->notice = $notice;
        }

        return $requestObject;
    }

}
