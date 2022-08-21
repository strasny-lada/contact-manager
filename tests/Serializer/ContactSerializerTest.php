<?php declare(strict_types = 1);

namespace App\Serializer;

use App\KernelTestCase;

class ContactSerializerTest extends KernelTestCase
{

    public function testSerializeContactListPageToJson(): void
    {
        $contactSerializer = $this->getServiceByType(ContactSerializer::class);

        self::assertSame(
            '{"page":{"url":"\/","title":"Spr\u00e1vce kontakt\u016f","content":"<table class=\"table table-responsive\"><thead><tr><th>Jm\u00e9no<\/th><th>E-mail<\/th><th>Telefon<\/th><th><\/th><\/tr><\/thead><\/table>"}}', // phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
            $contactSerializer->serializeContactListPageToJson(
                '/',
                'Správce kontaktů',
                '<table class="table table-responsive"><thead><tr><th>Jméno</th><th>E-mail</th><th>Telefon</th><th></th></tr></thead></table>',
            ),
        );
    }

}
