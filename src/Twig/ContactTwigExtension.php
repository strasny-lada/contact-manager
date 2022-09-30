<?php declare(strict_types = 1);

namespace App\Twig;

use App\Entity\Contact;
use App\Serializer\ContactSerializer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ContactTwigExtension extends AbstractExtension
{

    public function __construct(
        private readonly ContactSerializer $contactSerializer,
    )
    {
    }

    /**
     * @return \Twig\TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('serializeContactToJson', $this->serializeContactToJson(...)),
        ];
    }

    public function serializeContactToJson(Contact $contact): string
    {
        return $this->contactSerializer->serializeContactToJson($contact);
    }

}
