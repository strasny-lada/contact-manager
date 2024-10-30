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

        $response = self::parseJsonResponse($client->getResponse());
        self::assertNotNull($response['page'] ?? null);

        $pageData = $response['page'];

        self::assertNotNull($pageData['url'] ?? null);
        self::assertSame('/', $pageData['url']);

        self::assertNotNull($pageData['title'] ?? null);
        self::assertSame('Správce kontaktů', $pageData['title']);

        self::assertCount(3, $pageData['items']);
        $pageItems = $pageData['items'];

        // first row
        $pageItem = $pageItems[0];
        self::assertSame('Maxmilián', $pageItem['firstname']);
        self::assertSame('Pumpička', $pageItem['lastname']);
        self::assertSame('maxmilian@pumpicka.com', $pageItem['email']);
        self::assertSame('123456789', $pageItem['phone']);
        self::assertSame('Lorem ipsum dolor sit amet', $pageItem['notice']);
        self::assertSame('pumpicka-maxmilian', $pageItem['slug']);

        // second row
        $pageItem = $pageItems[1];
        self::assertSame('Gertruda', $pageItem['firstname']);
        self::assertSame('Pyšná', $pageItem['lastname']);
        self::assertSame('gertruda@pysna.com', $pageItem['email']);
        self::assertNull($pageItem['phone']);
        self::assertNull($pageItem['notice']);
        self::assertSame('pysna-gertruda', $pageItem['slug']);

        // third row
        $pageItem = $pageItems[2];
        self::assertSame('Harry', $pageItem['firstname']);
        self::assertSame('Šroubek', $pageItem['lastname']);
        self::assertSame('harry@sroubek.com', $pageItem['email']);
        self::assertSame('456789123', $pageItem['phone']);
        self::assertSame('Quisque facilisis, velit vel efficitur rutrum, nunc elit porta sem', $pageItem['notice']);
        self::assertSame('sroubek-harry', $pageItem['slug']);
    }

    public function testListNextPage(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $client->request('GET', '/api/contact/list/2');

        $response = self::parseJsonResponse($client->getResponse());
        self::assertNotNull($response['page'] ?? null);

        $pageData = $response['page'];

        self::assertNotNull($pageData['url'] ?? null);
        self::assertSame('/strana/2', $pageData['url']);

        self::assertNotNull($pageData['title'] ?? null);
        self::assertSame('Správce kontaktů - Strana 2', $pageData['title']);

        self::assertCount(1, $pageData['items']);
        $pageItems = $pageData['items'];

        // first row
        $pageItem = $pageItems[0];
        self::assertSame('Hugo', $pageItem['firstname']);
        self::assertSame('Ventil', $pageItem['lastname']);
        self::assertSame('hugo@ventil.com', $pageItem['email']);
        self::assertNull($pageItem['phone']);
        self::assertNull($pageItem['notice']);
        self::assertSame('ventil-hugo', $pageItem['slug']);
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
        self::assertSame(200, $client->getResponse()->getStatusCode());

        $response = self::parseJsonResponse($client->getResponse());
        self::assertNotNull($response['page'] ?? null);

        $pageData = $response['page'];

        self::assertSame(2, $pageData['number']);

        self::assertNotNull($pageData['url'] ?? null);
        self::assertSame('/strana/2', $pageData['url']);

        self::assertNotNull($pageData['title'] ?? null);
        self::assertSame('Správce kontaktů - Strana 2', $pageData['title']);
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

    public function testFetchDeleteContactForm(): void
    {
        IntegrationDatabaseTestCase::thisTestDoesNotChangeDatabase();
        $client = self::createClient();

        $client->request('GET', '/api/contact/delete-form');
        self::assertSame(200, $client->getResponse()->getStatusCode());

        $response = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertNotNull($response['csrf_token']);
        self::assertNotEmpty($response['csrf_token']);
    }

    public function testContactCanBeDeleted(): void
    {
        // fetch delete form
        $client = self::createClient();

        $client->request('GET', '/api/contact/delete-form');
        self::assertSame(200, $client->getResponse()->getStatusCode());

        $formResponse = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertNotEmpty($formResponse['csrf_token']);

        // delete contact
        $contact = ContactDatabaseFixture::$contactMaxmilian;

        $client->request('DELETE', '/api/contact/delete/' . $contact->getSlug(), [
            'contact_delete_form' => [
                '_token' => $formResponse['csrf_token'],
            ],
        ]);

        self::assertSame(204, $client->getResponse()->getStatusCode());

        // test entity deleted in the database
        /** @var \App\Entity\Contact|null $contact */
        $contact = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('contact')
            ->from(Contact::class, 'contact')
            ->andWhere('contact.id = :id')->setParameter('id', $contact->getId())
            ->getQuery()
            ->getOneOrNullResult();

        self::assertNull($contact);
    }

    public function testContactCannotBeDeletedWithInvalidCsrf(): void
    {
        $client = self::createClient();

        $contact = ContactDatabaseFixture::$contactMaxmilian;

        $client->request('DELETE', '/api/contact/delete/' . $contact->getSlug(), [
            'contact_delete_form' => [
                '_token' => 'invalid_token',
            ],
        ]);

        self::assertSame(400, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('Invalid CSRF token', (string) $client->getResponse()->getContent());

        // test entity was not deleted in the database
        /** @var \App\Entity\Contact|null $contact */
        $contact = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('contact')
            ->from(Contact::class, 'contact')
            ->andWhere('contact.id = :id')->setParameter('id', $contact->getId())
            ->getQuery()
            ->getOneOrNullResult();

        self::assertNotNull($contact);
    }

}
