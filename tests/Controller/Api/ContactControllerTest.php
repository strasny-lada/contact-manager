<?php declare(strict_types = 1);

namespace App\Controller\Api;

use App\Fixtures\ContactDatabaseFixture;
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

    public function testAddFormCanBeRendered(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $client->request('GET', '/pridat-kontakt');
        Assert::assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testEditFormCanBeRendered(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $contact = ContactDatabaseFixture::$contactMaxmilian;

        $client->request('GET', '/' . $contact->getSlug());
        Assert::assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testEditFormReturns404WithUnknownContact(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $client->request('GET', '/unknown-contact');
        Assert::assertSame(404, $client->getResponse()->getStatusCode());
    }

}
