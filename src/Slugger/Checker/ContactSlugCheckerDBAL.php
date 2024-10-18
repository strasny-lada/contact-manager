<?php declare(strict_types = 1);

namespace App\Slugger\Checker;

use Doctrine\DBAL\Connection;

// phpcs:disable PSR1.Files.SideEffects
final readonly class ContactSlugCheckerDBAL implements ContactSlugChecker
{

    public function __construct(
        private Connection $connection,
    )
    {
    }

    public function isValid(string $slug): bool
    {
        $result = $this->connection->fetchOne('SELECT COUNT(id) FROM `contact` WHERE `slug` = :slug', [
            'slug' => $slug,
        ]);

        return $result === false || $result === 0;
    }

}
