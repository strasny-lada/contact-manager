<?php declare(strict_types = 1);

namespace App\Serializer;

final class ContactSerializer
{

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
