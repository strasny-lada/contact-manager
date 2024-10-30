<?php declare(strict_types = 1);

namespace App\Dto;

use PHPUnit\Framework\TestCase;

final class ContactListPageDtoTest extends TestCase
{

    public function testToArray(): void
    {
        $contactListPageDto = new ContactListPageDto(
            1,
            'Správa kontaktů',
            '/',
            [
                new ContactDto(
                    'Hugo',
                    'Pumpička',
                    'hugo.pumpicka2@gmail.com',
                    '777123456',
                    'Lorem ipsum dolor sit amet',
                    'hugo-pumpicka',
                ),
                new ContactDto(
                    'Gertruda',
                    'Pyšná',
                    'gertruda@pysna.com',
                    null,
                    null,
                    'pysna-gertruda',
                ),
            ],
            [
                'current' => 1,
                'last' => 2,
            ]
        );

        $contactListPageArray = $contactListPageDto->toArray();

        self::assertSame(1, $contactListPageArray['number']);
        self::assertSame('Správa kontaktů', $contactListPageArray['title']);
        self::assertSame('/', $contactListPageArray['url']);

        self::assertCount(2, $contactListPageArray['items']);

        $contactListItemHugo = $contactListPageArray['items'][0];
        self::assertSame('Hugo', $contactListItemHugo['firstname']);
        self::assertSame('Pumpička', $contactListItemHugo['lastname']);

        $contactListItemGertruda = $contactListPageArray['items'][1];
        self::assertSame('Gertruda', $contactListItemGertruda['firstname']);
        self::assertSame('Pyšná', $contactListItemGertruda['lastname']);

        self::assertSame(1, $contactListPageArray['paginationData']['current']);
        self::assertSame(2, $contactListPageArray['paginationData']['last']);
    }

}
