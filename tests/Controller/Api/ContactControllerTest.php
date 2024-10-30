<?php declare(strict_types = 1);

namespace App\Controller\Api;

use App\IntegrationDatabaseTestCase;
use App\WebTestCase;
use PHPUnit\Framework\Assert;

class ContactControllerTest extends WebTestCase
{

    public function testListFirstPage(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $client->request('GET', '/');
        Assert::assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testListNextPage(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $client->request('GET', '/strana/2');
        Assert::assertSame(200, $client->getResponse()->getStatusCode());
    }

}
