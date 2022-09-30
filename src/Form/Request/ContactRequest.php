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

}
