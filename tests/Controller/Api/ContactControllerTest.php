<?php declare(strict_types = 1);

namespace App\Controller\Api;

use App\ApiTestCase;
use App\IntegrationDatabaseTestCase;

class ContactControllerTest extends ApiTestCase
{

    public function testListFirstPage(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $client->request('GET', 'api/contact/list/1');

        $responseHtmlCrawler = self::parseCrawlerFromJsonResponse(
            200,
            $client->getResponse(),
            'page/content',
        );

        self::assertCount(3, $responseHtmlCrawler->filter('.test-contact-row'));

        // first row
        $row1 = $responseHtmlCrawler->filter('.test-contact-row')->eq(0);
        self::assertSame('Pumpička Maxmilián', $row1->filter('.test-contact-name')->text());
        self::assertNotNull($row1->filter('.test-contact-name A')->attr('href'));
        self::assertSame(
            '/pumpicka-maxmilian',
            $row1->filter('.test-contact-name A')->attr('href')
        );
        self::assertSame('maxmilian@pumpicka.com', $row1->filter('.test-contact-email')->text());
        self::assertSame('123456789', $row1->filter('.test-contact-phone')->text());
        self::assertSame(
            '{"name":"Pumpi\u010dka Maxmili\u00e1n","notice":"Lorem ipsum dolor sit amet"}',
            $row1->filter('.test-contact-notice-button')->attr('data-notice-object'),
        );
        self::assertNotNull($row1->filter('.test-contact-edit-link')->attr('href'));
        self::assertSame(
            '/pumpicka-maxmilian',
            $row1->filter('.test-contact-edit-link')->attr('href')
        );
        self::assertNotNull($row1->filter('.test-contact-delete-link')->attr('href'));
        self::assertSame(
            '/pumpicka-maxmilian/odstraneni',
            $row1->filter('.test-contact-delete-link')->attr('href')
        );

        // second row
        $row2 = $responseHtmlCrawler->filter('.test-contact-row')->eq(1);
        self::assertSame('Pyšná Gertruda', $row2->filter('.test-contact-name')->text());
        self::assertSame('-', $row2->filter('.test-contact-phone')->text());
        self::assertSame(
            '{"name":"Py\u0161n\u00e1 Gertruda","notice":null}',
            $row2->filter('.test-contact-notice-button')->attr('data-notice-object'),
        );

        // third row
        $row3 = $responseHtmlCrawler->filter('.test-contact-row')->eq(2);
        self::assertSame('Šroubek Harry', $row3->filter('.test-contact-name')->text());

        $response = self::parseJsonResponse($client->getResponse());

        self::assertSame('/', $response['page']['url']);
        self::assertSame('Správce kontaktů', $response['page']['title']);
    }

    public function testListNextPage(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $client->request('GET', 'api/contact/list/2');

        $responseHtmlCrawler = self::parseCrawlerFromJsonResponse(
            200,
            $client->getResponse(),
            'page/content',
        );

        self::assertCount(1, $responseHtmlCrawler->filter('.test-contact-row'));

        $row1 = $responseHtmlCrawler->filter('.test-contact-row')->eq(0);
        self::assertSame('Ventil Hugo', $row1->filter('.test-contact-name')->text());

        $response = self::parseJsonResponse($client->getResponse());

        self::assertSame('/strana/2', $response['page']['url']);
        self::assertSame('Správce kontaktů - Strana 2', $response['page']['title']);
    }

    public function testListCanReturnInvalidParameterError(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $client->request('GET', 'api/contact/list/--non-numeric--');
        self::assertSame(404, $client->getResponse()->getStatusCode());
    }

    public function testListCanReturnPaginationOutOfRangeError(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $client->request('GET', 'api/contact/list/0');

        self::assertJsonResponse(400, [
            'error' => [
                'type' => 'pagination-out-of-range',
                'message' => 'Invalid page number. Page: 0: $page must be positive non-zero integer',
            ],
        ], $client->getResponse());
    }

    public function testListCanReturnResultOutOfRangeError(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $client->request('GET', 'api/contact/list/3');

        self::assertJsonResponse(400, [
            'error' => [
                'type' => 'result-out-of-range',
                'message' => 'Page number "3" is out of range',
            ],
        ], $client->getResponse());
    }

}
