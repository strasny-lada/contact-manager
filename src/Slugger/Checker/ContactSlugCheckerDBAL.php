<?php declare(strict_types = 1);

namespace App\Slugger\Checker;

use Doctrine\DBAL\Connection;

final class ContactSlugCheckerDBAL implements ContactSlugChecker
{

    public function __construct(
        private readonly Connection $connection,
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
