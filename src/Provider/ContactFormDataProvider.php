<?php declare(strict_types = 1);

namespace App\Provider;

use App\Dto\ContactDto;
use App\Dto\ContactFormDataDto;
use App\Entity\Contact;
use App\Serializer\ContactSerializer;
use Symfony\Contracts\Translation\TranslatorInterface;

// phpcs:disable PSR1.Files.SideEffects
final readonly class ContactFormDataProvider
{

    public function __construct(
        private ContactSerializer $contactSerializer,
        private TranslatorInterface $translator,
    )
    {
    }

    public function provideSerializedContactFormData(Contact $contact): string
    {
        $contactFormDataDto = $this->provideContactFormData($contact);

        if ($contactFormDataDto->getContactDto() === null) {
            throw new \Exception('Contact DTO should not be null at this point');
        }

        return $this->contactSerializer->serializeContactFormToJson(
            $contactFormDataDto->getContactDto(),
        );
    }

    public function provideContactFormData(?Contact $contact): ContactFormDataDto
    {
        return new ContactFormDataDto(
            $contact !== null ? ContactDto::fromContact($contact) : null,
            [
                'app.contact.firstname' => $this->translator->trans('app.contact.firstname'),
                'app.contact.lastname' => $this->translator->trans('app.contact.lastname'),
                'app.contact.email' => $this->translator->trans('app.contact.email'),
                'app.contact.phone' => $this->translator->trans('app.contact.phone'),
                'app.contact.notice' => $this->translator->trans('app.contact.notice'),
                'app.form.add' => $this->translator->trans('app.form.add'),
                'app.form.flash_message.added.success' => $this->translator->trans('app.form.flash_message.added.success'),
            ]
        );
    }

}
