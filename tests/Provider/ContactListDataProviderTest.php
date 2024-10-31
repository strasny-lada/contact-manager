<?php declare(strict_types = 1);

namespace App\Provider;

use App\Entity\Contact;
use App\Value\EmailAddress;
use App\Value\PhoneNumber;
use App\WebTestCase;

final class ContactListDataProviderTest extends WebTestCase
{

    public function testProvideSerializedContactListPageData(): void
    {
        $contactListDataProvider = $this->getServiceByType(ContactListDataProvider::class);

        $pageItems = [
            new Contact(
                'Hugo',
                'Pumpička',
                EmailAddress::fromString('hugo.pumpicka2@gmail.com'),
                PhoneNumber::fromString('777123456'),
                'Lorem ipsum dolor sit amet',
                'hugo-pumpicka',
            ),
            new Contact(
                'Gertruda',
                'Pyšná',
                EmailAddress::fromString('gertruda@pysna.com'),
                null,
                null,
                'pysna-gertruda',
            ),
        ];

        $serializedContactListData = $contactListDataProvider->provideSerializedContactListPageData(
            3,
            $pageItems,
            [
                'current' => 3,
                'last' => 4,
            ],
        );

        self::assertSame(
            '{"page":{"number":3,"title":"Spr\u00e1vce kontakt\u016f - Strana 3","url":"\/strana\/3","items":[{"firstname":"Hugo","lastname":"Pumpi\u010dka","email":"hugo.pumpicka2@gmail.com","phone":"777123456","notice":"Lorem ipsum dolor sit amet","slug":"hugo-pumpicka"},{"firstname":"Gertruda","lastname":"Py\u0161n\u00e1","email":"gertruda@pysna.com","phone":null,"notice":null,"slug":"pysna-gertruda"}],"paginationData":{"current":3,"last":4}},"texts":{"app.contact.list.empty":"Nebyl nalezen\u00fd \u017e\u00e1dn\u00fd kontakt","app.contact.name":"Jm\u00e9no","app.contact.email":"E-mail","app.contact.phone":"Telefon","app.contact.notice":"Pozn\u00e1mka","app.pagination.previous":"P\u0159edchoz\u00ed","app.pagination.next":"Dal\u0161\u00ed","app.form.add":"P\u0159idat nov\u00fd"},"urls":{"contact_add":"\/pridat-kontakt"}}', // phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
            $serializedContactListData,
        );
    }

}
