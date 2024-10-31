<?php declare(strict_types = 1);

namespace App\Serializer;

use App\Dto\ContactDto;
use App\Dto\ContactListPageDto;
use App\Entity\Contact;

final class ContactSerializer
{

    public function serializeContactToJson(Contact $contact): string
    {
        return json_encode([
            'name' => $contact->getName(),
            'notice' => $contact->getNotice(),
        ], JSON_THROW_ON_ERROR);
    }

    /**
     * @param array<string, string> $texts
     * @param array<string, string> $urls
     */
    public function serializeContactListPageToJson(
        ContactListPageDto $contactListPage,
        array $texts,
        array $urls,
    ): string
    {
        return json_encode([
            'page' => $contactListPage->toArray(),
            'texts' => $texts,
            'urls' => $urls,
        ], JSON_THROW_ON_ERROR);
    }

    public function serializeContactFormToJson(
        ContactDto $contactDto,
    ): string
    {
        return json_encode([
            'contact' => $contactDto->toArray(),
        ], JSON_THROW_ON_ERROR);
    }

}
