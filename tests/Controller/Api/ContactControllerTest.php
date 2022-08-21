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

        $responseHtmlCrawler = $client->request('GET', '/');
        Assert::assertSame(200, $client->getResponse()->getStatusCode());

        self::assertCount(3, $responseHtmlCrawler->filter('.test-contact-row'));

        // first row
        $row1 = $responseHtmlCrawler->filter('.test-contact-row')->eq(0);
        self::assertSame('Pumpička Maxmilián', $row1->filter('.test-contact-name')->text());
        self::assertSame('maxmilian@pumpicka.com', $row1->filter('.test-contact-email')->text());
        self::assertSame('123456789', $row1->filter('.test-contact-phone')->text());

        // second row
        $row2 = $responseHtmlCrawler->filter('.test-contact-row')->eq(1);
        self::assertSame('Pyšná Gertruda', $row2->filter('.test-contact-name')->text());
        self::assertSame('-', $row2->filter('.test-contact-phone')->text());

        // third row
        $row3 = $responseHtmlCrawler->filter('.test-contact-row')->eq(2);
        self::assertSame('Šroubek Harry', $row3->filter('.test-contact-name')->text());
    }

    public function testListNextPage(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $responseHtmlCrawler = $client->request('GET', '/strana/2');
        Assert::assertSame(200, $client->getResponse()->getStatusCode());

        self::assertCount(1, $responseHtmlCrawler->filter('.test-contact-row'));

        // test row
        $row3 = $responseHtmlCrawler->filter('.test-contact-row')->eq(0);
        self::assertSame('Ventil Hugo', $row3->filter('.test-contact-name')->text());
    }

}
