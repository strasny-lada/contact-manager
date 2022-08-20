<?php declare(strict_types = 1);

namespace App;

abstract class KernelTestCase extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{

    /**
     * @template T
     * @phpstan-param class-string<T> $type
     * @phpstan-return T
     */
    protected function getServiceByType(string $type) // intentionally omitting return type to not overwrite inferred type
    {
        /** @phpstan-var T|null $service */ // phpcs:ignore SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation
        $service = static::getContainer()->get($type);
        if ($service === null) {
            throw new \Exception(sprintf('Service "%s" not found in container', $type));
        }
        return $service;
    }

}
