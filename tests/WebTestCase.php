<?php declare(strict_types = 1);

namespace App;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

abstract class WebTestCase extends KernelTestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $container = self::$kernel->getContainer();

        /** @var \Symfony\Bundle\FrameworkBundle\KernelBrowser $client */
        $client = $container->get('test.client');

        IntegrationDatabaseTestCase::rebuildDatabase($client->getKernel());
    }

    protected function getEntityManager(): EntityManager
    {
        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        return $entityManager;
    }

    /**
     * @param string[] $options An array of options to pass to the createKernel class
     * @param string[] $server An array of server parameters
     */
    protected static function createClient(array $options = [], array $server = []): KernelBrowser
    {
        self::bootKernel($options);

        $container = self::$kernel->getContainer();

        /** @var \Symfony\Bundle\FrameworkBundle\KernelBrowser $client */
        $client = $container->get('test.client');

        $client->setServerParameters($server);

        $client->disableReboot();

        return $client;
    }

}
