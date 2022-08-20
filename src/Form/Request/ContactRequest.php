<?php declare(strict_types = 1);

// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint

namespace App\Form\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class ContactRequest
{

    /**
     * @Assert\NotBlank()
     * @var string
     */
    public $firstname;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    public $lastname;

    /**
     * @Assert\NotBlank()
     * @Assert\Email(
     *     mode="strict"
     * )
     * @var string
     */
    public $email;

    /** @var string|null */
    public $phone;

    /** @var string|null */
    public $notice;

}
