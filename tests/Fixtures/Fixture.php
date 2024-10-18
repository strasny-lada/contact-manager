<?php declare(strict_types = 1);

namespace App\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture as DoctrineFixture;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

abstract class Fixture extends DoctrineFixture
{

    final public function load(ObjectManager $manager): void
    {
        if (!($manager instanceof EntityManager)) {
            throw new \Exception('Given instance is not ORM EntityManager');
        }
        $this->loadWithEntityManager($manager);
    }

    /**
     * Ensures that Fixtures get only EntityManager and not ObjectManager
     */
    abstract public function loadWithEntityManager(EntityManager $entityManager): void;

    /**
     * @template T of object
     * @phpstan-param T $entity
     * @phpstan-return T
     */
    public static function findEntity($entity, EntityManagerInterface $entityManager)
    {
        $class = $entityManager->getClassMetadata($entity::class);

        $id = $class->getIdentifierValues($entity);
        $singleValueId = $id[$class->identifier[0]] ?? null;

        /** @phpstan-var class-string<T> $rootEntityName */ // phpcs:ignore SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation
        $rootEntityName = $class->rootEntityName;

        /** @phpstan-var T|null $object */ // phpcs:ignore SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation
        $object = $entityManager->find($rootEntityName, $singleValueId);
        if ($object === null) {
            throw new \Exception('reloaded entity cannot ever be null');
        }

        return $object;
    }

}
