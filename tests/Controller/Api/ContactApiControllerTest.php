<?php declare(strict_types = 1);

namespace App\Controller\Api;

use App\ApiTestCase;
use App\Entity\Contact;
use App\Fixtures\ContactDatabaseFixture;
use App\IntegrationDatabaseTestCase;

final class ContactApiControllerTest extends ApiTestCase
{

    public function testListFirstPage(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $client->request('GET', '/api/contact/list/1');

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

        $client->request('GET', '/api/contact/list/2');

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

        $client->request('GET', '/api/contact/list/--non-numeric--');
        self::assertSame(404, $client->getResponse()->getStatusCode());
    }

    public function testListCanReturnPaginationOutOfRangeError(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $client->request('GET', '/api/contact/list/0');
        self::assertSame(400, $client->getResponse()->getStatusCode());
    }

    public function testListCanReturnResultOutOfRangeError(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $client->request('GET', '/api/contact/list/3');
        self::assertSame(400, $client->getResponse()->getStatusCode());
    }

    public function testFetchAddContactForm(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $client->request('GET', '/api/contact/add-form');
        self::assertSame(200, $client->getResponse()->getStatusCode());

        $response = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertNotNull($response['csrf_token']);
        self::assertNotEmpty($response['csrf_token']);
    }

    public function testContactCanBeCreated(): void
    {
        // fetch add form
        $client = self::createClient();

        $client->request('GET', '/api/contact/add-form');
        self::assertSame(200, $client->getResponse()->getStatusCode());

        $formResponse = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertNotEmpty($formResponse['csrf_token']);

        // create contact
        $client->request('POST', '/api/contact/add', [
            'contact_form' => [
                'firstname' => 'Hugo',
                'lastname' => 'Pumpička',
                'email' => 'hugo.pumpicka2@gmail.com',
                'phone' => '777123456',
                'notice' => 'Lorem ipsum dolor sit amet',
                '_token' => $formResponse['csrf_token'],
            ],
        ]);

        self::assertSame(201, $client->getResponse()->getStatusCode());

        $response = json_decode((string) $client->getResponse()->getContent(), true);

        // test contact data in the response
        $responseContact = $response['contact'] ?? null;
        self::assertNotNull($responseContact);

        self::assertSame('Hugo', $responseContact['firstname']);
        self::assertSame('Pumpička', $responseContact['lastname']);
        self::assertSame('hugo.pumpicka2@gmail.com', $responseContact['email']);
        self::assertSame('777123456', $responseContact['phone']);
        self::assertSame('Lorem ipsum dolor sit amet', $responseContact['notice']);
        self::assertSame('pumpicka-hugo', $responseContact['slug']);

        // test entity created in the database
        /** @var \App\Entity\Contact|null $contact */
        $contact = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('contact')
            ->from(Contact::class, 'contact')
            ->andWhere('contact.email = :email')->setParameter('email', 'hugo.pumpicka2@gmail.com')
            ->getQuery()
            ->getOneOrNullResult();

        self::assertNotNull($contact);

        self::assertSame('Hugo', $contact->getFirstname());
        self::assertSame('Pumpička', $contact->getLastname());
        self::assertSame('hugo.pumpicka2@gmail.com', $contact->getEmail()->toString());
        self::assertNotNull($contact->getPhone());
        self::assertSame('777123456', $contact->getPhone()->toString());
        self::assertSame('Lorem ipsum dolor sit amet', $contact->getNotice());
        self::assertSame('pumpicka-hugo', $contact->getSlug());
    }

    public function testContactCanBeCreatedWithRequiredFieldsOnly(): void
    {
        // fetch add form
        $client = self::createClient();

        $client->request('GET', '/api/contact/add-form');
        self::assertSame(200, $client->getResponse()->getStatusCode());

        $formResponse = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertNotEmpty($formResponse['csrf_token']);

        // create contact
        $client->request('POST', '/api/contact/add', [
            'contact_form' => [
                'firstname' => 'Hugo',
                'lastname' => 'Pumpička',
                'email' => 'hugo.pumpicka2@gmail.com',
                'phone' => '',
                'notice' => '',
                '_token' => $formResponse['csrf_token'],
            ],
        ]);

        self::assertSame(201, $client->getResponse()->getStatusCode());

        $response = json_decode((string) $client->getResponse()->getContent(), true);

        // test contact data in the response
        $responseContact = $response['contact'] ?? null;
        self::assertNotNull($responseContact);

        self::assertSame('Hugo', $responseContact['firstname']);
        self::assertSame('Pumpička', $responseContact['lastname']);
        self::assertSame('hugo.pumpicka2@gmail.com', $responseContact['email']);
        self::assertNull($responseContact['phone']);
        self::assertNull($responseContact['notice']);
        self::assertSame('pumpicka-hugo', $responseContact['slug']);

        // test entity created in the database
        /** @var \App\Entity\Contact|null $contact */
        $contact = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('contact')
            ->from(Contact::class, 'contact')
            ->andWhere('contact.email = :email')->setParameter('email', 'hugo.pumpicka2@gmail.com')
            ->getQuery()
            ->getOneOrNullResult();

        self::assertNotNull($contact);

        self::assertSame('Hugo', $contact->getFirstname());
        self::assertSame('Pumpička', $contact->getLastname());
        self::assertSame('hugo.pumpicka2@gmail.com', $contact->getEmail()->toString());
        self::assertNull($contact->getPhone());
        self::assertNull($contact->getNotice());
        self::assertSame('pumpicka-hugo', $contact->getSlug());
    }

    public function testContactCannotBeCreatedWithIncompleteData(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();

        // fetch add form
        $client = self::createClient();

        $client->request('GET', '/api/contact/add-form');
        self::assertSame(200, $client->getResponse()->getStatusCode());

        $formResponse = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertNotEmpty($formResponse['csrf_token']);

        // create contact
        $client->request('POST', '/api/contact/add', [
            'contact_form' => [
                'firstname' => 'Hugo',
                'lastname' => '', // empty lastname
                'email' => 'hugo.pumpicka2@gmail.com',
                'phone' => '',
                'notice' => '',
                '_token' => $formResponse['csrf_token'],
            ],
        ]);

        self::assertSame(400, $client->getResponse()->getStatusCode());

        // validation error - missing lastname
        self::assertSame(400, $client->getResponse()->getStatusCode());
        self::assertStringContainsString(
            'Validation failed: Object(App\Form\Request\ContactRequest).lastname',
            (string) $client->getResponse()->getContent(),
        );

        // test a new entity was not created
        /** @var \App\Entity\Contact|null $contact */
        $contact = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('contact')
            ->from(Contact::class, 'contact')
            ->andWhere('contact.email = :email')->setParameter('email', 'hugo.pumpicka2@gmail.com')
            ->getQuery()
            ->getOneOrNullResult();

        self::assertNull($contact);
    }

    public function testFetchEditContactForm(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $contact = ContactDatabaseFixture::$contactGertruda;

        $client->request('GET', '/api/contact/edit-form/' . $contact->getSlug());
        self::assertSame(200, $client->getResponse()->getStatusCode());

        $response = json_decode((string) $client->getResponse()->getContent(), true);

        $responseContact = $response['contact'] ?? null;
        self::assertNotNull($responseContact);

        self::assertSame('Gertruda', $responseContact['firstname']);
        self::assertSame('Pyšná', $responseContact['lastname']);
        self::assertSame('gertruda@pysna.com', $responseContact['email']);
        self::assertNull($responseContact['phone']);
        self::assertNull($responseContact['notice']);
        self::assertSame('pysna-gertruda', $responseContact['slug']);

        self::assertNotNull($response['csrf_token']);
        self::assertNotEmpty($response['csrf_token']);
    }

    public function testContactCanBeUpdated(): void
    {
        $contact = ContactDatabaseFixture::$contactMaxmilian;

        // fetch edit form
        $client = self::createClient();

        $client->request('GET', '/api/contact/edit-form/' . $contact->getSlug());
        self::assertSame(200, $client->getResponse()->getStatusCode());

        $formResponse = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertNotEmpty($formResponse['csrf_token']);

        // update contact
        self::assertSame('Maxmilián', $contact->getFirstname());
        self::assertSame('Pumpička', $contact->getLastname());
        self::assertSame('maxmilian@pumpicka.com', $contact->getEmail()->toString());
        self::assertNotNull($contact->getPhone());
        self::assertSame('123456789', $contact->getPhone()->toString());
        self::assertSame('Lorem ipsum dolor sit amet', $contact->getNotice());
        self::assertSame('pumpicka-maxmilian', $contact->getSlug());

        $client->request('PUT', '/api/contact/edit/' . $contact->getSlug(), [
            'contact_form' => [
                'firstname' => 'Maxmiliánek',
                'lastname' => 'Pumpička',
                'email' => 'maxmilian.pumpicka@gmail.com',
                'phone' => '',
                'notice' => '',
                '_token' => $formResponse['csrf_token'],
            ],
        ]);

        self::assertSame(204, $client->getResponse()->getStatusCode());

        // test entity updated in the database
        /** @var \App\Entity\Contact|null $contact */
        $contact = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('contact')
            ->from(Contact::class, 'contact')
            ->andWhere('contact.id = :id')->setParameter('id', $contact->getId())
            ->getQuery()
            ->getOneOrNullResult();

        self::assertNotNull($contact);

        self::assertSame('Maxmiliánek', $contact->getFirstname());
        self::assertSame('Pumpička', $contact->getLastname());
        self::assertSame('maxmilian.pumpicka@gmail.com', $contact->getEmail()->toString());
        self::assertNull($contact->getPhone());
        self::assertNull($contact->getNotice());
        self::assertSame('pumpicka-maxmilianek', $contact->getSlug());
    }

    public function testContactCannotBeUpdatedWithIncompleteData(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $contact = ContactDatabaseFixture::$contactMaxmilian;

        // fetch edit form
        $client = self::createClient();

        $client->request('GET', '/api/contact/edit-form/' . $contact->getSlug());
        self::assertSame(200, $client->getResponse()->getStatusCode());

        $formResponse = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertNotEmpty($formResponse['csrf_token']);

        // update contact
        self::assertSame('Maxmilián', $contact->getFirstname());
        self::assertSame('Pumpička', $contact->getLastname());
        self::assertSame('maxmilian@pumpicka.com', $contact->getEmail()->toString());
        self::assertNotNull($contact->getPhone());
        self::assertSame('123456789', $contact->getPhone()->toString());
        self::assertSame('Lorem ipsum dolor sit amet', $contact->getNotice());
        self::assertSame('pumpicka-maxmilian', $contact->getSlug());

        $client->request('PUT', '/api/contact/edit/' . $contact->getSlug(), [
            'contact_form' => [
                'firstname' => 'Maxmiliánek',
                'lastname' => 'Pumpička',
                'email' => '', // empty email
                'phone' => '',
                'notice' => '',
                '_token' => $formResponse['csrf_token'],
            ],
        ]);

        // validation error - missing email
        self::assertSame(400, $client->getResponse()->getStatusCode());
        self::assertStringContainsString(
            'Validation failed: Object(App\Form\Request\ContactRequest).email',
            (string) $client->getResponse()->getContent(),
        );

        // test entity updated in the database
        /** @var \App\Entity\Contact|null $contact */
        $contact = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('contact')
            ->from(Contact::class, 'contact')
            ->andWhere('contact.id = :id')->setParameter('id', $contact->getId())
            ->getQuery()
            ->getOneOrNullResult();

        self::assertNotNull($contact);

        // contact is not changed
        self::assertSame('Maxmilián', $contact->getFirstname());
        self::assertSame('Pumpička', $contact->getLastname());
        self::assertSame('maxmilian@pumpicka.com', $contact->getEmail()->toString());
    }

}
