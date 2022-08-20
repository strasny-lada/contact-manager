<?php declare(strict_types = 1);

namespace App;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class IntegrationDatabaseTestCase extends KernelTestCase
{

    private static bool $dbInitDone = false;

    private static bool $databaseDoesNotNeedToBeCleaned = false;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        self::rebuildDatabase(self::$kernel);
    }

    /**
     * Necessary since Symfony 5.3 as it deprecates the session service and the session must be accessed through Request
     */
    protected function pushRequestWithSessionToRequestStack(): void
    {
        /** @var \Symfony\Component\HttpFoundation\RequestStack $requestStack */
        $requestStack = $this->getServiceByType(RequestStack::class);

        if ($requestStack->getMainRequest() !== null) {
            throw new \Exception(sprintf('There is already a request for URL "%s" available', $requestStack->getMainRequest()->getBaseUrl()));
        }

        // push new request with session
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));
        $requestStack->push($request);
    }

    public static function thisTestDoesNotChangeDatabase(): void
    {
        self::$databaseDoesNotNeedToBeCleaned = true;
    }

    public static function rebuildDatabase(KernelInterface $kernel): void
    {
        self::initDatabase($kernel);

        if (self::$databaseDoesNotNeedToBeCleaned) {
            self::$databaseDoesNotNeedToBeCleaned = false;
            return;
        }
        self::$databaseDoesNotNeedToBeCleaned = false;

        $container = $kernel->getContainer();

        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $container->get('doctrine.orm.default_entity_manager');
        $dbalConnection = $entityManager->getConnection();

        // workaround to "help" with fixtures purging.
        $dbalConnection->executeQuery('SET FOREIGN_KEY_CHECKS=0');

        /** @var \Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader $loader */
        $loader = $container->get('test.doctrine.fixtures.loader');

        $purger = new ORMPurger();
        $executor = new ORMExecutor($entityManager, $purger);

        // database is purged first and the FOREIGN_KEY_CHECKS is reenabled to catch any potential issues
        $executor->purge();
        $dbalConnection->executeQuery('SET FOREIGN_KEY_CHECKS=1');

        // executed with append=true to skip purging step
        $executor->execute($loader->getFixtures(), true);
    }

    private static function initDatabase(KernelInterface $kernel): void
    {
        if (self::$dbInitDone) {
            return;
        }

        $application = self::getApplication($kernel);

        self::runCommand($application, 'doctrine:database:create', [
            '--if-not-exists' => true,
        ]);
        self::runCommand($application, 'doctrine:schema:update', [
            '--force' => true,
            '--complete' => true,
        ]);

        self::$dbInitDone = true;
    }

    /**
     * @param mixed[] $arguments key value array of arguments
     */
    public static function runCommand(
        Application $application,
        string $command,
        array $arguments = []
    ): int
    {
        $outputStream = fopen('php://temp', 'r+');
        if ($outputStream === false) {
            throw new \Exception('Cannot open output stream');
        }

        $arguments['--env'] = 'test';
        $arguments = array_merge(['command' => $command], $arguments);

        $return = $application->run(
            new ArrayInput($arguments),
            new StreamOutput($outputStream, StreamOutput::VERBOSITY_VERY_VERBOSE)
        );

        if ($return !== 0) {
            rewind($outputStream);
            $errors = stream_get_contents($outputStream);
            fclose($outputStream);
            throw new \Exception(json_encode($errors, JSON_THROW_ON_ERROR));
        }
        fclose($outputStream);

        return $return;
    }

    public static function getApplication(KernelInterface $kernel): Application
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $application->setCatchExceptions(true);

        return $application;
    }

    protected function getEntityManager(): EntityManager
    {
        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        return $entityManager;
    }

}
