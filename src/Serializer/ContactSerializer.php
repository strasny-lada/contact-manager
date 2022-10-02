<?php declare(strict_types = 1);

namespace App\Serializer;

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

    public function serializeContactListPageToJson(
        string $pageUrl,
        string $pageTitle,
        string $pageContent,
    ): string
    {
        return json_encode([
            'page' => [
                'url' => $pageUrl,
                'title' => $pageTitle,
                'content' => $pageContent,
            ],
        ], JSON_THROW_ON_ERROR);
    }

}
