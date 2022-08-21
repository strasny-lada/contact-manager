<?php declare(strict_types = 1);

namespace App\Serializer;

use App\Entity\Contact;

final class ContactSerializer
{

    public static function serializeToJson(Contact $contact): string
    {
        return json_encode([
            'name' => $contact->getName(),
            'notice' => $contact->getNotice(),
        ], JSON_THROW_ON_ERROR);
    }

}
